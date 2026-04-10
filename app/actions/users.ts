"use server"

import { prisma } from "@/lib/prisma"
import bcrypt from "bcryptjs"
import { revalidatePath } from "next/cache"

export async function fetchAllUsers() {
  const users = await prisma.user.findMany({
    orderBy: { createdAt: "desc" },
    include: {
      _count: {
        select: { bookings: true }
      }
    }
  })

  return users.map(user => ({
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role.toLowerCase() as any,
    isActive: user.isActive,
    createdAt: user.createdAt,
    bookingCount: user._count.bookings
  }))
}

export async function createUser(data: { name: string; email: string; password: string; role: string }) {
  try {
    console.log("[createUser] Starting user creation for:", data.email)
    const existingUser = await prisma.user.findUnique({
      where: { email: data.email }
    })

    if (existingUser) {
      console.warn("[createUser] Email already exists:", data.email)
      return { success: false, error: "Email sudah terdaftar" }
    }

    const password = data.password || "password123"
    const hashedPassword = await bcrypt.hash(password, 10)

    const user = await prisma.user.create({
      data: {
        name: data.name,
        email: data.email,
        password: hashedPassword,
        role: data.role.toUpperCase(),
        isActive: true
      }
    })

    console.log("[createUser] User created successfully:", user.id)
    revalidatePath("/admin/users")
    
    // Remove password before returning
    const { password: _, ...userWithoutPassword } = user
    return { success: true, user: userWithoutPassword }
  } catch (error: any) {
    console.error("[createUser] Error creating user:", error)
    return { success: false, error: error.message || "Gagal membuat user" }
  }
}

export async function updateUser(userId: string, data: { name?: string; email?: string; role?: string }) {
  try {
    console.log("[updateUser] Updating user:", userId, data)
    await prisma.user.update({
      where: { id: userId },
      data: {
        ...data,
        role: data.role ? data.role.toUpperCase() : undefined
      }
    })

    revalidatePath("/admin/users")
    return { success: true }
  } catch (error: any) {
    console.error("[updateUser] Error updating user:", error)
    return { success: false, error: error.message || "Gagal memperbarui user" }
  }
}

export async function toggleUserStatus(userId: string, isActive: boolean, reason?: string, duration?: number) {
  try {
    console.log("[toggleUserStatus] Toggling status for:", userId, "to:", isActive)
    await prisma.$transaction(async (tx) => {
      // 1. Update user status
      await tx.user.update({
        where: { id: userId },
        data: { isActive }
      })

      // 2. If deactivating, create record
      if (!isActive) {
        await tx.userDeactivation.create({
          data: {
            userId,
            reason: reason || "Tanpa alasan",
            deactivatedBy: "Admin", // Should ideally be current admin's name
            duration,
            reactivateAt: duration ? new Date(Date.now() + duration * 24 * 60 * 60 * 1000) : undefined,
          }
        })
      }
    })

    revalidatePath("/admin/users")
    return { success: true }
  } catch (error: any) {
    console.error("[toggleUserStatus] Error toggling user status:", error)
    return { success: false, error: error.message || "Gagal mengubah status user" }
  }
}

export async function deleteUser(userId: string) {
  try {
    console.log("[deleteUser] Deleting user:", userId)
    await prisma.user.delete({
      where: { id: userId }
    })

    revalidatePath("/admin/users")
    return { success: true }
  } catch (error: any) {
    console.error("[deleteUser] Error deleting user:", error)
    return { success: false, error: error.message || "Gagal menghapus user" }
  }
}

export async function updateUserProfile(userId: string, data: { name?: string; email?: string; password?: string }) {
  try {
    console.log("[updateUserProfile] Updating profile for:", userId)
    const updateData: any = {
      name: data.name,
      email: data.email,
    }

    if (data.password) {
      updateData.password = await bcrypt.hash(data.password, 10)
    }

    await prisma.user.update({
      where: { id: userId },
      data: updateData
    })

    return { success: true }
  } catch (error: any) {
    console.error("[updateUserProfile] Error updating profile:", error)
    return { success: false, error: error.message || "Gagal memperbarui profil" }
  }
}
