"use client"

import { useState, useEffect } from "react"
import { Search } from "lucide-react"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { GridListToggle } from "@/components/ui/grid-list-toggle"
import { EquipmentCard } from "@/components/user/equipment-card"
import { NewBookingModal } from "@/components/user/new-booking-modal"
<<<<<<< HEAD
import {
  getEquipment,
  getBookings,
  saveBookings,
  initializeLocalStorage,
  updateStockAvailability,
} from "@/lib/data-manager"
=======
import { fetchEquipment, createBooking } from "@/app/actions/assets"
import { getCurrentUserAction } from "@/app/actions/auth"
>>>>>>> 95064e54 (init: setup project and add authorization checks)
import type { BookingFormData, Equipment } from "@/lib/types"
import { toast } from "sonner"

export default function EquipmentPage() {
  const [equipment, setEquipment] = useState<Equipment[]>([])
  const [view, setView] = useState<"grid" | "list">("grid")
  const [search, setSearch] = useState("")
  const [statusFilter, setStatusFilter] = useState<string>("all")
  const [categoryFilter, setCategoryFilter] = useState<string>("all")
  const [bookingModalOpen, setBookingModalOpen] = useState(false)
  const [selectedEquipmentId, setSelectedEquipmentId] = useState<string | null>(null)

  useEffect(() => {
<<<<<<< HEAD
    initializeLocalStorage()
    loadEquipment()
  }, [bookingModalOpen])

  const loadEquipment = () => {
    updateStockAvailability()
    const loadedEquipment = getEquipment()
    setEquipment(loadedEquipment)
=======
    loadEquipment()
  }, [bookingModalOpen])

  const loadEquipment = async () => {
    const response = await fetchEquipment()
    if (response.success) {
      setEquipment(response.data)
    } else {
      toast.error(response.error || "Gagal memuat daftar barang")
    }
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  const categories = Array.from(new Set(equipment.map((e) => e.category)))

  const filteredEquipment = equipment.filter((equipment) => {
    const matchesSearch = equipment.name.toLowerCase().includes(search.toLowerCase())
    const matchesStatus = statusFilter === "all" || equipment.status === statusFilter
    const matchesCategory = categoryFilter === "all" || equipment.category === categoryFilter
    return matchesSearch && matchesStatus && matchesCategory
  })

  const handleBook = (equipmentId: string) => {
    setSelectedEquipmentId(equipmentId)
    setBookingModalOpen(true)
  }

<<<<<<< HEAD
  const handleBookingSubmit = (data: BookingFormData) => {
    const authData = localStorage.getItem("sipinjam_auth")
    if (!authData) {
=======
  const handleBookingSubmit = async (data: BookingFormData) => {
    const user = await getCurrentUserAction()
    if (!user) {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
      toast.error("Sesi tidak valid, silakan login kembali")
      return
    }

<<<<<<< HEAD
    const user = JSON.parse(authData)
    const existingBookings = getBookings()

    const newBooking = {
      id: `booking-${Date.now()}`,
      userId: user.id,
      userName: user.name,
      type: "equipment" as const,
      itemId: selectedEquipmentId!,
      itemName: equipment.find((e) => e.id === selectedEquipmentId)?.name || "",
      startDate: data.startDate,
      endDate: data.endDate,
      purpose: data.purpose,
      notes: data.notes,
      status: "pending" as const,
      createdAt: new Date(),
    }

    existingBookings.push(newBooking)
    saveBookings(existingBookings)

    toast.success("Peminjaman berhasil diajukan!", {
      description: "Admin akan meninjau permintaan Anda dalam 2x24 jam",
    })
    setBookingModalOpen(false)
    setSelectedEquipmentId(null)
=======
    const response = await createBooking({
      ...data,
      userId: user.id,
      userName: user.name,
    })

    if (response.success) {
      toast.success("Peminjaman berhasil diajukan!", {
        description: "Admin akan meninjau permintaan Anda dalam 2x24 jam",
      })
      setBookingModalOpen(false)
      setSelectedEquipmentId(null)
    } else {
      toast.error(response.error || "Gagal mengajukan peminjaman")
    }
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Daftar Barang</h1>
        <p className="text-muted-foreground">Pilih barang yang ingin Anda pinjam</p>
      </div>

      {/* Filters */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari barang..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="pl-9"
          />
        </div>
        <div className="flex items-center gap-2">
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="w-[140px]">
              <SelectValue placeholder="Status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Status</SelectItem>
              <SelectItem value="available">Tersedia</SelectItem>
              <SelectItem value="booked">Booking</SelectItem>
              <SelectItem value="in-use">Digunakan</SelectItem>
            </SelectContent>
          </Select>
          <Select value={categoryFilter} onValueChange={setCategoryFilter}>
            <SelectTrigger className="w-[140px]">
              <SelectValue placeholder="Kategori" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Kategori</SelectItem>
              {categories.map((category) => (
                <SelectItem key={category} value={category}>
                  {category}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          <GridListToggle view={view} onViewChange={setView} />
        </div>
      </div>

      {/* Results Count */}
      <div className="text-sm text-muted-foreground">
        Menampilkan {filteredEquipment.length} dari {equipment.length} barang
      </div>

      {/* Equipment Grid/List */}
      {filteredEquipment.length === 0 ? (
        <div className="flex min-h-[400px] items-center justify-center">
          <div className="text-center">
            <p className="text-lg font-semibold">Tidak ada barang ditemukan</p>
            <p className="text-sm text-muted-foreground">Coba ubah filter pencarian Anda</p>
          </div>
        </div>
      ) : (
        <div className={view === "grid" ? "grid gap-6 sm:grid-cols-2 lg:grid-cols-3" : "space-y-4"}>
          {filteredEquipment.map((equipment) => (
            <EquipmentCard key={equipment.id} equipment={equipment} view={view} onBook={handleBook} />
          ))}
        </div>
      )}

      <NewBookingModal
        open={bookingModalOpen}
        onOpenChange={setBookingModalOpen}
        onSubmit={handleBookingSubmit}
        preselectedType="equipment"
        preselectedItemId={selectedEquipmentId}
      />
    </div>
  )
}
