"use client"

import { useState, useEffect } from "react"
import { Plus, Search, Filter } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { EquipmentFormModal } from "@/components/admin/equipment-form-modal"
import { EquipmentCard } from "@/components/user/equipment-card"
import { ScheduleCalendar } from "@/components/admin/schedule-calendar"
import { toast } from "sonner"
import type { Equipment, Booking } from "@/lib/types"
<<<<<<< HEAD
import { mockEquipment, mockBookings } from "@/lib/mock-data"
=======
import { fetchEquipment, handleSaveEquipment as handleSaveEquipmentAction, handleDeleteEquipment } from "@/app/actions/assets"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

export default function EquipmentManagementPage() {
  const [equipment, setEquipment] = useState<Equipment[]>([])
  const [bookings, setBookings] = useState<Booking[]>([])
  const [searchQuery, setSearchQuery] = useState("")
  const [statusFilter, setStatusFilter] = useState<string>("all")
  const [categoryFilter, setCategoryFilter] = useState<string>("all")
  const [isFormOpen, setIsFormOpen] = useState(false)
  const [selectedEquipment, setSelectedEquipment] = useState<Equipment | undefined>()
  const [viewSchedule, setViewSchedule] = useState<Equipment | null>(null)

<<<<<<< HEAD
  useEffect(() => {
    const savedEquipment = localStorage.getItem("sipinjam_equipment")
    const savedBookings = localStorage.getItem("sipinjam_bookings")

    if (savedEquipment) {
      setEquipment(JSON.parse(savedEquipment))
    } else {
      setEquipment(mockEquipment)
      localStorage.setItem("sipinjam_equipment", JSON.stringify(mockEquipment))
    }

    if (savedBookings) {
      setBookings(JSON.parse(savedBookings))
    } else {
      setBookings(mockBookings)
    }
  }, [])

  const handleSaveEquipment = (equipmentData: Partial<Equipment>) => {
    let updatedEquipment: Equipment[]

    if (equipmentData.id) {
      // Update existing equipment
      updatedEquipment = equipment.map((item) =>
        item.id === equipmentData.id ? ({ ...item, ...equipmentData } as Equipment) : item,
      )
      toast.success("Barang berhasil diperbarui")
    } else {
      // Add new equipment
      const newEquipment: Equipment = {
        id: `eq-${Date.now()}`,
        name: equipmentData.name!,
        category: equipmentData.category!,
        quantity: equipmentData.quantity!,
        available: equipmentData.available!,
        status: equipmentData.status!,
        imageUrl: equipmentData.imageUrl || "/placeholder.svg",
        description: equipmentData.description!,
      }
      updatedEquipment = [...equipment, newEquipment]
      toast.success("Barang baru berhasil ditambahkan")
    }

    setEquipment(updatedEquipment)
    localStorage.setItem("sipinjam_equipment", JSON.stringify(updatedEquipment))
=======
  const loadEquipment = async () => {
    const response = await fetchEquipment()
    if (response.success) {
      setEquipment(response.data)
    } else {
      toast.error(response.error || "Gagal memuat daftar barang")
    }
  }

  useEffect(() => {
    loadEquipment()
  }, [])

  const handleSaveEquipment = async (equipmentData: Partial<Equipment>) => {
    const formData = new FormData()
    if (equipmentData.id) formData.append("id", equipmentData.id)
    formData.append("name", equipmentData.name || "")
    formData.append("category", equipmentData.category || "")
    formData.append("quantity", (equipmentData.quantity || 0).toString())
    formData.append("available", (equipmentData.available || 0).toString())
    formData.append("description", equipmentData.description || "")
    formData.append("imageUrl", equipmentData.imageUrl || "")

    try {
      const result = await handleSaveEquipmentAction(formData)
      if (result.success) {
        toast.success(equipmentData.id ? "Barang berhasil diperbarui" : "Barang baru berhasil ditambahkan")
        loadEquipment()
      }
    } catch (error) {
      toast.error("Gagal menyimpan barang")
    }
  }

  const handleDelete = async (item: Equipment) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus barang ${item.name}?`)) return
    try {
      const result = await handleDeleteEquipment(item.id)
      if (result.success) {
        toast.success("Barang berhasil dihapus")
        loadEquipment()
      }
    } catch (error) {
      toast.error("Gagal menghapus barang")
    }
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  const handleEditEquipment = (item: Equipment) => {
    setSelectedEquipment(item)
    setIsFormOpen(true)
  }

  const handleAddNew = () => {
    setSelectedEquipment(undefined)
    setIsFormOpen(true)
  }

  const filteredEquipment = equipment.filter((item) => {
    const matchesSearch =
      item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      item.category.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus = statusFilter === "all" || item.status === statusFilter
    const matchesCategory = categoryFilter === "all" || item.category === categoryFilter

    return matchesSearch && matchesStatus && matchesCategory
  })

  const categories = Array.from(new Set(equipment.map((e) => e.category)))

  const stats = {
    total: equipment.reduce((sum, item) => sum + item.quantity, 0),
    available: equipment.reduce((sum, item) => sum + item.available, 0),
    booked: equipment.reduce((sum, item) => sum + (item.quantity - item.available), 0),
    items: equipment.length,
  }

  if (viewSchedule) {
    return (
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Jadwal Barang</h1>
            <p className="text-muted-foreground">Lihat jadwal peminjaman barang</p>
          </div>
          <Button onClick={() => setViewSchedule(null)} variant="outline">
            Kembali ke Daftar Barang
          </Button>
        </div>

        <ScheduleCalendar bookings={bookings} itemId={viewSchedule.id} itemName={viewSchedule.name} />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Kelola Barang</h1>
          <p className="text-muted-foreground">Kelola semua barang dalam sistem</p>
        </div>
        <Button onClick={handleAddNew} className="bg-orange-600 hover:bg-orange-700">
          <Plus className="mr-2 h-4 w-4" />
          Tambah Barang
        </Button>
      </div>

      <div className="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Unit</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Tersedia</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-600">{stats.available}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Dipinjam</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-yellow-600">{stats.booked}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Jenis Barang</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.items}</div>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <div className="flex flex-col gap-4 md:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari barang atau kategori..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="pl-9"
          />
        </div>
        <Select value={statusFilter} onValueChange={setStatusFilter}>
          <SelectTrigger className="w-full md:w-[200px]">
            <Filter className="mr-2 h-4 w-4" />
            <SelectValue placeholder="Status" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Semua Status</SelectItem>
            <SelectItem value="available">Tersedia</SelectItem>
            <SelectItem value="booked">Booking</SelectItem>
            <SelectItem value="maintenance">Maintenance</SelectItem>
          </SelectContent>
        </Select>
        <Select value={categoryFilter} onValueChange={setCategoryFilter}>
          <SelectTrigger className="w-full md:w-[200px]">
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
      </div>

      {/* Equipment Grid */}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {filteredEquipment.map((item) => (
          <div key={item.id} className="space-y-2">
            <EquipmentCard equipment={item} view="grid" />
            <div className="flex gap-2">
              <Button variant="outline" className="flex-1 bg-transparent" onClick={() => handleEditEquipment(item)}>
                Edit
              </Button>
              <Button variant="outline" className="flex-1 bg-transparent" onClick={() => setViewSchedule(item)}>
                Lihat Jadwal
              </Button>
<<<<<<< HEAD
=======
              <Button variant="outline" className="flex-1 bg-transparent text-red-600 hover:text-red-700" onClick={() => handleDelete(item)}>
                Hapus
              </Button>
>>>>>>> 95064e54 (init: setup project and add authorization checks)
            </div>
          </div>
        ))}
      </div>

      {filteredEquipment.length === 0 && (
        <div className="py-12 text-center">
          <p className="text-muted-foreground">Tidak ada barang ditemukan</p>
        </div>
      )}

      <EquipmentFormModal
        open={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        equipment={selectedEquipment}
        onSave={handleSaveEquipment}
      />
    </div>
  )
}
