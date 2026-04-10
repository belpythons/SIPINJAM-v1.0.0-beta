"use client"

import { useState } from "react"
import { format, isValid } from "date-fns"
import { id as localeId } from "date-fns/locale"
import { Calendar, Clock, Package, DoorOpen, Download, Eye, Search, Filter } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Badge } from "@/components/ui/badge"
import type { Booking } from "@/lib/types"

interface BookingTableProps {
  bookings: Booking[]
  onViewDetail: (booking: Booking) => void
  onDownloadPDF: (booking: Booking) => void
}

function safeFormatDate(date: Date | string | undefined, formatStr: string): string {
  if (!date) return "Invalid Date"
  const dateObj = typeof date === "string" ? new Date(date) : date
  if (!isValid(dateObj)) return "Invalid Date"
  return format(dateObj, formatStr, { locale: localeId })
}

export function BookingTable({ bookings, onViewDetail, onDownloadPDF }: BookingTableProps) {
  const [searchQuery, setSearchQuery] = useState("")
  const [statusFilter, setStatusFilter] = useState<string>("all")
  const [typeFilter, setTypeFilter] = useState<string>("all")

  // Filter bookings
  const filteredBookings = bookings.filter((booking) => {
<<<<<<< HEAD
    const matchesSearch =
      booking.itemName.toLowerCase().includes(searchQuery.toLowerCase()) ||
      booking.purpose.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus = statusFilter === "all" || booking.status === statusFilter
=======
    const itemName = booking.itemName || ""
    const status = booking.status || ""
    const purpose = booking.purpose || ""

    const matchesSearch =
      itemName.toLowerCase().includes(searchQuery.toLowerCase()) ||
      purpose.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus = statusFilter === "all" || status.toLowerCase() === statusFilter.toLowerCase()
>>>>>>> 95064e54 (init: setup project and add authorization checks)
    const matchesType = typeFilter === "all" || booking.type === typeFilter

    return matchesSearch && matchesStatus && matchesType
  })

  const getStatusBadge = (status: Booking["status"]) => {
<<<<<<< HEAD
=======
    const normalizedStatus = (status?.toLowerCase() || "pending") as Booking["status"]
>>>>>>> 95064e54 (init: setup project and add authorization checks)
    const variants: Record<
      Booking["status"],
      { variant: "default" | "secondary" | "destructive" | "outline"; label: string }
    > = {
      pending: { variant: "secondary", label: "Menunggu" },
      approved: { variant: "default", label: "Disetujui" },
      rejected: { variant: "destructive", label: "Ditolak" },
      active: { variant: "default", label: "Aktif" },
      completed: { variant: "outline", label: "Selesai" },
      cancelled: { variant: "outline", label: "Dibatalkan" },
    }
<<<<<<< HEAD
    return variants[status]
=======
    return variants[normalizedStatus] || variants.pending
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  return (
    <div className="space-y-6">
      {/* Filters */}
      <div className="flex flex-col gap-4 md:flex-row md:items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari berdasarkan nama atau tujuan..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="pl-9"
          />
        </div>
        <div className="flex gap-2">
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="w-[150px]">
              <Filter className="mr-2 h-4 w-4" />
              <SelectValue placeholder="Status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Status</SelectItem>
              <SelectItem value="pending">Menunggu</SelectItem>
              <SelectItem value="approved">Disetujui</SelectItem>
              <SelectItem value="rejected">Ditolak</SelectItem>
              <SelectItem value="active">Aktif</SelectItem>
              <SelectItem value="completed">Selesai</SelectItem>
            </SelectContent>
          </Select>
          <Select value={typeFilter} onValueChange={setTypeFilter}>
            <SelectTrigger className="w-[150px]">
              <Filter className="mr-2 h-4 w-4" />
              <SelectValue placeholder="Tipe" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Tipe</SelectItem>
              <SelectItem value="room">Ruangan</SelectItem>
              <SelectItem value="equipment">Barang</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      {/* Bookings List */}
      {filteredBookings.length === 0 ? (
        <div className="rounded-lg border border-dashed p-12 text-center">
          <Package className="mx-auto h-12 w-12 text-muted-foreground" />
          <h3 className="mt-4 text-lg font-semibold">Tidak ada data</h3>
          <p className="mt-2 text-sm text-muted-foreground">
            {searchQuery || statusFilter !== "all" || typeFilter !== "all"
              ? "Tidak ada peminjaman yang sesuai dengan filter"
              : "Anda belum memiliki riwayat peminjaman"}
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          {filteredBookings.map((booking) => (
            <div key={booking.id} className="rounded-lg border bg-card p-6 transition-colors hover:bg-accent/50">
              <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div className="space-y-3">
                  <div className="flex flex-wrap items-center gap-2">
                    {booking.type === "room" ? (
                      <DoorOpen className="h-5 w-5 text-primary" />
                    ) : (
                      <Package className="h-5 w-5 text-primary" />
                    )}
                    <h3 className="text-lg font-semibold">{booking.itemName}</h3>
                    <Badge variant={getStatusBadge(booking.status).variant}>
                      {getStatusBadge(booking.status).label}
                    </Badge>
                  </div>
                  <div className="space-y-1 text-sm text-muted-foreground">
                    <div className="flex items-center gap-2">
                      <Calendar className="h-4 w-4" />
                      <span>{safeFormatDate(booking.startDate, "dd MMM yyyy")}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <Clock className="h-4 w-4" />
                      <span>
                        {safeFormatDate(booking.startDate, "HH:mm")} - {safeFormatDate(booking.endDate, "HH:mm")} WIB
                      </span>
                    </div>
                  </div>
                  <p className="text-sm">
                    <span className="font-medium">Tujuan:</span> {booking.purpose}
                  </p>
                </div>
                <div className="flex gap-2">
                  <Button variant="outline" size="sm" onClick={() => onViewDetail(booking)}>
                    <Eye className="mr-2 h-4 w-4" />
                    Detail
                  </Button>
                  {booking.status === "approved" && (
                    <Button variant="default" size="sm" onClick={() => onDownloadPDF(booking)}>
                      <Download className="mr-2 h-4 w-4" />
                      PDF
                    </Button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
