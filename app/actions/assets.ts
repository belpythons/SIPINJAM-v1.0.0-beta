"use server"

import { prisma } from "@/lib/prisma"
import { revalidatePath } from "next/cache"
import { createRoom, updateRoom, deleteRoom, createEquipment, updateEquipment, deleteEquipment } from "@/lib/assets"
import { getCurrentUserAction } from "@/app/actions/auth"

export async function getDashboardStats() {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  const [totalUsers, totalBookings, totalRooms, totalEquipment] = await Promise.all([
    prisma.user.count(),
    prisma.booking.count(),
    prisma.room.count(),
    prisma.equipment.count()
  ])

  const [pendingBookings, approvedBookings, rejectedBookings, completedBookings] = await Promise.all([
    prisma.booking.count({ where: { status: "PENDING" } }),
    prisma.booking.count({ where: { status: "APPROVED" } }),
    prisma.booking.count({ where: { status: "REJECTED" } }),
    prisma.booking.count({ where: { status: "COMPLETED" } })
  ])

  // Simple availability check - could be improved with real booking checks
  const availableRooms = await prisma.room.count() 
  const availableEquipment = await prisma.equipment.aggregate({
    _sum: { available: true }
  })

  // Get trend data for the last 6 months
  const now = new Date()
  const trendData = []
  for (let i = 5; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
    const monthName = d.toLocaleString("id-ID", { month: "short" })
    const nextMonth = new Date(d.getFullYear(), d.getMonth() + 1, 1)

    const count = await prisma.booking.count({
      where: {
        createdAt: {
          gte: d,
          lt: nextMonth,
        },
      },
    })

    trendData.push({ month: monthName, bookings: count })
  }

  return {
    totalUsers,
    totalBookings,
    pendingBookings,
    approvedBookings,
    rejectedBookings,
    completedBookings,
    totalRooms,
    totalEquipment,
    availableRooms,
    availableEquipment: availableEquipment._sum.available || 0,
    trendData,
  }
}

export async function fetchRooms() {
  try {
    const rooms = await prisma.room.findMany({
      orderBy: { name: "asc" }
    })
    const formattedRooms = rooms.map(r => ({
      ...r,
      facilities: r.description ? r.description.split(", ") : [],
      status: "available" as const // Standardize for the frontend
    }))
    return { success: true, data: formattedRooms }
  } catch (error) {
    console.error("Error fetching rooms:", error)
    return { success: false, error: "Gagal memuat daftar ruangan" }
  }
}

export async function fetchEquipment() {
  try {
    const equipment = await prisma.equipment.findMany({
      where: {
        available: {
          gt: 0
        }
      },
      orderBy: { name: "asc" }
    })
    const formattedEquipment = equipment.map(e => ({
      ...e,
      status: e.available > 0 ? "available" as const : "booked" as const
    }))
    return { success: true, data: formattedEquipment }
  } catch (error) {
    console.error("Error fetching equipment:", error)
    return { success: false, error: "Gagal memuat daftar barang" }
  }
}

export async function getUserDashboardStats(userId: string) {
  try {
    const session = await getCurrentUserAction()
    if (!session) return { success: false, error: "Unauthorized" }
    if (session.id !== userId) return { success: false, error: "Forbidden" }

    const [total, active, pending, completed] = await Promise.all([
      prisma.booking.count({ where: { userId } }),
      prisma.booking.count({ 
        where: { 
          userId, 
          status: { in: ["APPROVED", "ACTIVE"] } 
        } 
      }),
      prisma.booking.count({ where: { userId, status: "PENDING" } }),
      prisma.booking.count({ where: { userId, status: "COMPLETED" } }),
    ])

    // Get trend data for the last 6 months
    const now = new Date()
    const trendData = []
    for (let i = 5; i >= 0; i--) {
      const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
      const monthName = d.toLocaleString("id-ID", { month: "short" })
      const nextMonth = new Date(d.getFullYear(), d.getMonth() + 1, 1)

      const count = await prisma.booking.count({
        where: {
          userId,
          createdAt: {
            gte: d,
            lt: nextMonth,
          },
        },
      })

      trendData.push({ month: monthName, bookings: count })
    }

    return {
      success: true,
      data: {
        total,
        active,
        pending,
        completed,
        trendData,
      }
    }
  } catch (error) {
    console.error("Error fetching user dashboard stats:", error)
    return { success: false, error: "Gagal memuat statistik dashboard" }
  }
}

export async function createBooking(data: {
  userId: string
  userName: string
  type: "room" | "equipment"
  itemId: string
  startDate: Date
  endDate: Date
  purpose: string
  notes?: string
}) {
  try {
    const session = await getCurrentUserAction()
    if (!session) return { success: false, error: "Unauthorized" }
    
    // Force the userId to be the one from the session
    const userId = session.id

    // 1. Validate schedule conflicts
    const conflicts = await prisma.booking.findMany({
      where: {
        type: data.type,
        ...(data.type === "room" 
          ? { roomId: data.itemId } 
          : { equipmentId: data.itemId }),
        status: { in: ["PENDING", "APPROVED", "ACTIVE"] },
        OR: [
          {
            startDate: { lte: data.startDate },
            endDate: { gte: data.startDate },
          },
          {
            startDate: { lte: data.endDate },
            endDate: { gte: data.endDate },
          },
          {
            startDate: { gte: data.startDate },
            endDate: { lte: data.endDate },
          },
        ],
      },
    })

    if (conflicts.length > 0) {
      return { success: false, error: "Jadwal sudah terisi oleh peminjaman lain." }
    }

    // 2. Create the booking
    const item = data.type === "room" 
      ? await prisma.room.findUnique({ where: { id: data.itemId } })
      : await prisma.equipment.findUnique({ where: { id: data.itemId } })

    if (!item) {
      return { success: false, error: "Item tidak ditemukan" }
    }

    const booking = await prisma.booking.create({
      data: {
        userId,
        type: data.type,
        roomId: data.type === "room" ? data.itemId : null,
        equipmentId: data.type === "equipment" ? data.itemId : null,
        startDate: data.startDate,
        endDate: data.endDate,
        purpose: data.purpose,
        notes: data.notes,
        status: "PENDING",
      },
    })

    revalidatePath("/admin/dashboard")
    revalidatePath("/user/dashboard")
    revalidatePath("/user/bookings")

    const formattedBooking = {
      ...booking,
      status: booking.status.toLowerCase() as any,
      itemId: booking.type === "room" ? booking.roomId! : booking.equipmentId!,
      itemName: item.name,
      userName: data.userName
    }
    return { success: true, data: formattedBooking }
  } catch (error: any) {
    console.error("Booking error:", error)
    return { success: false, error: error.message || "Gagal mengajukan peminjaman" }
  }
}

export async function updateBookingStatus(
  bookingId: string,
  status: "APPROVED" | "REJECTED" | "COMPLETED" | "CANCELLED",
  adminNotes?: string,
  rejectionReason?: string
) {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  const booking = await prisma.booking.findUnique({
    where: { id: bookingId },
    include: {
      user: true,
      room: true,
      equipment: true
    }
  })

  if (!booking) throw new Error("Booking tidak ditemukan")

  // 3. Stock Management Logic
  if (booking.type === "equipment" && booking.equipmentId) {
    if (status === "APPROVED") {
      // Decrease stock
      await prisma.equipment.update({
        where: { id: booking.equipmentId },
        data: { available: { decrement: 1 } },
      })
    } else if (status === "COMPLETED" || (booking.status === "APPROVED" && status === "CANCELLED")) {
      // Increase stock back
      await prisma.equipment.update({
        where: { id: booking.equipmentId },
        data: { available: { increment: 1 } },
      })
    }
  }

  const updatedBooking = await prisma.booking.update({
    where: { id: bookingId },
    data: {
      status,
      notes: adminNotes || booking.notes,
      rejectionReason,
      approvedAt: status === "APPROVED" ? new Date() : undefined,
    },
    include: {
      user: true,
      room: true,
      equipment: true
    }
  })

  revalidatePath("/admin/dashboard")
  revalidatePath("/admin/bookings")
  revalidatePath("/user/dashboard")
  revalidatePath("/user/bookings")

  return {
    ...updatedBooking,
    status: updatedBooking.status.toLowerCase() as any,
    itemId: updatedBooking.type === "room" ? updatedBooking.roomId! : updatedBooking.equipmentId!,
    itemName: updatedBooking.type === "room" ? updatedBooking.room?.name : updatedBooking.equipment?.name,
    userName: updatedBooking.user?.name
  }
}

export async function fetchUserBookings(userId: string) {
  try {
    const session = await getCurrentUserAction()
    if (!session) return { success: false, error: "Unauthorized" }
    if (session.id !== userId && session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

    const bookings = await prisma.booking.findMany({
    where: { userId },
    orderBy: { createdAt: "desc" },
    include: {
      user: true,
      room: true,
      equipment: true,
    },
  })

    return bookings.map(booking => ({
      ...booking,
      status: booking.status.toLowerCase() as any,
      itemId: booking.type === "room" ? booking.roomId! : booking.equipmentId!,
      itemName: booking.type === "room" ? booking.room?.name : booking.equipment?.name,
      userName: booking.user?.name
    }))
  } catch (error) {
    console.error("Error fetching user bookings:", error)
    return { success: false, error: "Gagal memuat daftar peminjaman" }
  }
}

export async function fetchAllBookings() {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  const bookings = await prisma.booking.findMany({
    orderBy: { createdAt: "desc" },
    include: {
      user: true,
      room: true,
      equipment: true,
    },
  })

  return bookings.map(booking => ({
    ...booking,
    status: booking.status.toLowerCase() as any,
    itemId: booking.type === "room" ? booking.roomId! : booking.equipmentId!,
    itemName: booking.type === "room" ? booking.room?.name : booking.equipment?.name,
    userName: booking.user?.name
  }))
}

export async function fetchUserDashboardStats(userId: string) {
  const [total, pending, approved, rejected, completed] = await Promise.all([
    prisma.booking.count({ where: { userId } }),
    prisma.booking.count({ where: { userId, status: "PENDING" } }),
    prisma.booking.count({ where: { userId, status: "APPROVED" } }),
    prisma.booking.count({ where: { userId, status: "REJECTED" } }),
    prisma.booking.count({ where: { userId, status: "COMPLETED" } }),
  ])

  return {
    total,
    pending,
    approved,
    rejected,
    completed,
    active: approved // Assuming approved means active for user display
  }
}

export async function fetchRules() {
  return await prisma.rule.findMany({
    orderBy: { order: "asc" }
  })
}

export async function fetchNotifications(userId: string) {
  return await prisma.notification.findMany({
    where: { userId },
    orderBy: { createdAt: "desc" }
  })
}

export async function markNotificationAsRead(notificationId: string) {
  try {
    await prisma.notification.update({
      where: { id: notificationId },
      data: { isRead: true }
    })
    return { success: true }
  } catch (error: any) {
    return { success: false, error: error.message }
  }
}

export async function handleSaveRoom(formData: any) {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  const id = formData.get("id")
  const data = {
    name: formData.get("name"),
    capacity: parseInt(formData.get("capacity")),
    description: formData.get("description"),
    building: formData.get("building"),
    floor: parseInt(formData.get("floor")),
    imageUrl: formData.get("imageUrl") || "/placeholder.svg",
  }

  if (id) {
    await updateRoom(id, data)
  } else {
    await createRoom(data)
  }

  revalidatePath("/admin/rooms")
  return { success: true }
}

export async function handleDeleteRoom(id: string) {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  await deleteRoom(id)
  revalidatePath("/admin/rooms")
  return { success: true }
}

export async function handleSaveEquipment(formData: any) {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  const id = formData.get("id")
  const data = {
    name: formData.get("name"),
    category: formData.get("category"),
    quantity: parseInt(formData.get("quantity")),
    available: parseInt(formData.get("available")),
    description: formData.get("description"),
    imageUrl: formData.get("imageUrl") || "/placeholder.svg",
  }

  if (id) {
    await updateEquipment(id, data)
  } else {
    await createEquipment(data)
  }

  revalidatePath("/admin/equipment")
  return { success: true }
}

export async function handleDeleteEquipment(id: string) {
  const session = await getCurrentUserAction()
  if (!session) return { success: false, error: "Unauthorized" }
  if (session.role.toUpperCase() !== "ADMIN") return { success: false, error: "Forbidden" }

  await deleteEquipment(id)
  revalidatePath("/admin/equipment")
  return { success: true }
}
