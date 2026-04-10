"use client"

import { useState, useEffect } from "react"
import { Plus, Search, Filter } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { RoomFormModal } from "@/components/admin/room-form-modal"
import { RoomCard } from "@/components/user/room-card"
import { ScheduleCalendar } from "@/components/admin/schedule-calendar"
import { toast } from "sonner"
import type { Room, Booking } from "@/lib/types"
<<<<<<< HEAD
import { mockRooms, mockBookings } from "@/lib/mock-data"
=======
import { fetchRooms, handleSaveRoom as handleSaveRoomAction, handleDeleteRoom } from "@/app/actions/assets"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

export default function RoomsManagementPage() {
  const [rooms, setRooms] = useState<Room[]>([])
  const [bookings, setBookings] = useState<Booking[]>([])
  const [searchQuery, setSearchQuery] = useState("")
  const [statusFilter, setStatusFilter] = useState<string>("all")
  const [buildingFilter, setBuildingFilter] = useState<string>("all")
  const [isFormOpen, setIsFormOpen] = useState(false)
  const [selectedRoom, setSelectedRoom] = useState<Room | undefined>()
  const [viewSchedule, setViewSchedule] = useState<Room | null>(null)

<<<<<<< HEAD
  useEffect(() => {
    const savedRooms = localStorage.getItem("sipinjam_rooms")
    const savedBookings = localStorage.getItem("sipinjam_bookings")

    if (savedRooms) {
      setRooms(JSON.parse(savedRooms))
    } else {
      setRooms(mockRooms)
      localStorage.setItem("sipinjam_rooms", JSON.stringify(mockRooms))
    }

    if (savedBookings) {
      setBookings(JSON.parse(savedBookings))
    } else {
      setBookings(mockBookings)
    }
  }, [])

  const handleSaveRoom = (roomData: Partial<Room>) => {
    let updatedRooms: Room[]

    if (roomData.id) {
      // Update existing room
      updatedRooms = rooms.map((room) => (room.id === roomData.id ? ({ ...room, ...roomData } as Room) : room))
      toast.success("Ruangan berhasil diperbarui")
    } else {
      // Add new room
      const newRoom: Room = {
        id: `room-${Date.now()}`,
        name: roomData.name!,
        capacity: roomData.capacity!,
        facilities: roomData.facilities!,
        status: roomData.status!,
        imageUrl: roomData.imageUrl || "/placeholder.svg",
        building: roomData.building!,
        floor: roomData.floor!,
      }
      updatedRooms = [...rooms, newRoom]
      toast.success("Ruangan baru berhasil ditambahkan")
    }

    setRooms(updatedRooms)
    localStorage.setItem("sipinjam_rooms", JSON.stringify(updatedRooms))
=======
  const loadRooms = async () => {
    const response = await fetchRooms()
    if (response.success) {
      setRooms(response.data)
    } else {
      toast.error(response.error || "Gagal memuat daftar ruangan")
    }
  }

  useEffect(() => {
    loadRooms()
  }, [])

  const handleSaveRoom = async (roomData: Partial<Room>) => {
    const formData = new FormData()
    if (roomData.id) formData.append("id", roomData.id)
    formData.append("name", roomData.name || "")
    formData.append("capacity", (roomData.capacity || 0).toString())
    formData.append("description", roomData.facilities?.join(", ") || "")
    formData.append("building", roomData.building || "")
    formData.append("floor", (roomData.floor || 0).toString())
    formData.append("imageUrl", roomData.imageUrl || "")

    try {
      const result = await handleSaveRoomAction(formData)
      if (result.success) {
        toast.success(roomData.id ? "Ruangan berhasil diperbarui" : "Ruangan baru berhasil ditambahkan")
        loadRooms()
      }
    } catch (error) {
      toast.error("Gagal menyimpan ruangan")
    }
  }

  const handleDelete = async (room: Room) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus ruangan ${room.name}?`)) return
    try {
      const result = await handleDeleteRoom(room.id)
      if (result.success) {
        toast.success("Ruangan berhasil dihapus")
        loadRooms()
      }
    } catch (error) {
      toast.error("Gagal menghapus ruangan")
    }
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  const handleEditRoom = (room: Room) => {
    setSelectedRoom(room)
    setIsFormOpen(true)
  }

  const handleAddNew = () => {
    setSelectedRoom(undefined)
    setIsFormOpen(true)
  }

  const filteredRooms = rooms.filter((room) => {
    const matchesSearch =
      room.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      room.building.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus = statusFilter === "all" || room.status === statusFilter
    const matchesBuilding = buildingFilter === "all" || room.building === buildingFilter

    return matchesSearch && matchesStatus && matchesBuilding
  })

  const buildings = Array.from(new Set(rooms.map((r) => r.building)))

  const stats = {
    total: rooms.length,
    available: rooms.filter((r) => r.status === "available").length,
    booked: rooms.filter((r) => r.status === "booked").length,
    inUse: rooms.filter((r) => r.status === "in-use").length,
  }

  if (viewSchedule) {
    return (
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Jadwal Ruangan</h1>
            <p className="text-muted-foreground">Lihat jadwal peminjaman ruangan</p>
          </div>
          <Button onClick={() => setViewSchedule(null)} variant="outline">
            Kembali ke Daftar Ruangan
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
          <h1 className="text-3xl font-bold">Kelola Ruangan</h1>
          <p className="text-muted-foreground">Kelola semua ruangan dalam sistem</p>
        </div>
        <Button onClick={handleAddNew} className="bg-orange-600 hover:bg-orange-700">
          <Plus className="mr-2 h-4 w-4" />
          Tambah Ruangan
        </Button>
      </div>

      {/* Stats */}
      <div className="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Ruangan</CardTitle>
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
            <CardTitle className="text-sm font-medium">Booking</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-yellow-600">{stats.booked}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Digunakan</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-red-600">{stats.inUse}</div>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <div className="flex flex-col gap-4 md:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari ruangan atau gedung..."
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
            <SelectItem value="in-use">Digunakan</SelectItem>
          </SelectContent>
        </Select>
        <Select value={buildingFilter} onValueChange={setBuildingFilter}>
          <SelectTrigger className="w-full md:w-[200px]">
            <SelectValue placeholder="Gedung" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Semua Gedung</SelectItem>
            {buildings.map((building) => (
              <SelectItem key={building} value={building}>
                {building}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      {/* Room Grid */}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {filteredRooms.map((room) => (
          <div key={room.id} className="space-y-2">
            <RoomCard room={room} view="grid" />
            <div className="flex gap-2">
              <Button variant="outline" className="flex-1 bg-transparent" onClick={() => handleEditRoom(room)}>
                Edit
              </Button>
              <Button variant="outline" className="flex-1 bg-transparent" onClick={() => setViewSchedule(room)}>
                Lihat Jadwal
              </Button>
<<<<<<< HEAD
=======
              <Button variant="outline" className="flex-1 bg-transparent text-red-600 hover:text-red-700" onClick={() => handleDelete(room)}>
                Hapus
              </Button>
>>>>>>> 95064e54 (init: setup project and add authorization checks)
            </div>
          </div>
        ))}
      </div>

      {filteredRooms.length === 0 && (
        <div className="py-12 text-center">
          <p className="text-muted-foreground">Tidak ada ruangan ditemukan</p>
        </div>
      )}

      <RoomFormModal
        open={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        room={selectedRoom}
        onSave={handleSaveRoom}
      />
    </div>
  )
}
