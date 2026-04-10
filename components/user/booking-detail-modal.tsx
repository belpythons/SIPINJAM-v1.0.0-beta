"use client"

import { format, isValid, differenceInDays, differenceInHours } from "date-fns"
import { id as localeId } from "date-fns/locale"
import {
  Calendar,
  Clock,
  User,
  Package,
  DoorOpen,
  FileText,
  CheckCircle,
  XCircle,
  AlertCircle,
  Hash,
  Timer,
} from "lucide-react"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Badge } from "@/components/ui/badge"
import { Card, CardContent } from "@/components/ui/card"
import type { Booking } from "@/lib/types"

interface BookingDetailModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  booking: Booking | null
}

function safeFormatDate(date: Date | string | undefined, formatStr: string): string {
  if (!date) return "Invalid Date"
  const dateObj = typeof date === "string" ? new Date(date) : date
  if (!isValid(dateObj)) return "Invalid Date"
  return format(dateObj, formatStr, { locale: localeId })
}

export function BookingDetailModal({ open, onOpenChange, booking }: BookingDetailModalProps) {
  if (!booking) return null

<<<<<<< HEAD
=======
  const status = (booking.status?.toLowerCase() || "pending") as Booking["status"]

>>>>>>> 95064e54 (init: setup project and add authorization checks)
  const calculateDuration = () => {
    const start = typeof booking.startDate === "string" ? new Date(booking.startDate) : booking.startDate
    const end = typeof booking.endDate === "string" ? new Date(booking.endDate) : booking.endDate

    if (!isValid(start) || !isValid(end)) return "Invalid dates"

    const days = differenceInDays(end, start)
    const hours = differenceInHours(end, start) % 24

    if (days > 0) {
      return `${days} hari ${hours > 0 ? `${hours} jam` : ""}`
    }
    return `${hours} jam`
  }

  const getStatusIcon = () => {
<<<<<<< HEAD
    switch (booking.status) {
=======
    switch (status) {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
      case "approved":
        return <CheckCircle className="h-5 w-5 text-green-600" />
      case "rejected":
        return <XCircle className="h-5 w-5 text-red-600" />
      case "pending":
        return <AlertCircle className="h-5 w-5 text-yellow-600" />
      case "active":
        return <Timer className="h-5 w-5 text-blue-600" />
      case "completed":
        return <CheckCircle className="h-5 w-5 text-gray-600" />
      default:
        return <FileText className="h-5 w-5 text-blue-600" />
    }
  }

<<<<<<< HEAD
  const getStatusBadge = (status: Booking["status"]) => {
=======
  const getStatusBadge = (currentStatus: Booking["status"]) => {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
    const variants: Record<
      Booking["status"],
      { variant: "default" | "secondary" | "destructive" | "outline"; label: string; color: string }
    > = {
      pending: { variant: "secondary", label: "Menunggu Persetujuan", color: "bg-yellow-100 text-yellow-800" },
      approved: { variant: "default", label: "Disetujui", color: "bg-green-100 text-green-800" },
      rejected: { variant: "destructive", label: "Ditolak", color: "bg-red-100 text-red-800" },
      active: { variant: "default", label: "Sedang Berlangsung", color: "bg-blue-100 text-blue-800" },
      completed: { variant: "outline", label: "Selesai", color: "bg-gray-100 text-gray-800" },
      cancelled: { variant: "outline", label: "Dibatalkan", color: "bg-gray-100 text-gray-800" },
    }
<<<<<<< HEAD
    return variants[status]
  }

  const getStatusColor = () => {
    switch (booking.status) {
=======
    return variants[currentStatus] || variants.pending
  }

  const getStatusColor = () => {
    switch (status) {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
      case "approved":
        return "border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950"
      case "rejected":
        return "border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950"
      case "pending":
        return "border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950"
      case "active":
        return "border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950"
      default:
        return "border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950"
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2 text-xl">
            {getStatusIcon()}
            Detail Peminjaman
          </DialogTitle>
          <DialogDescription className="flex items-center gap-2">
            <Hash className="h-3 w-3" />
            ID: {booking.id}
          </DialogDescription>
        </DialogHeader>

        <div className="space-y-6">
          <Card className={`border-2 ${getStatusColor()}`}>
            <CardContent className="pt-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Status Peminjaman</p>
<<<<<<< HEAD
                  <p className="mt-1 text-2xl font-bold">{getStatusBadge(booking.status).label}</p>
=======
                  <p className="mt-1 text-2xl font-bold">
                    {getStatusBadge(status).label}
                  </p>
>>>>>>> 95064e54 (init: setup project and add authorization checks)
                </div>
                <div className="flex h-16 w-16 items-center justify-center rounded-full bg-background">
                  {getStatusIcon()}
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="pt-6">
              <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                {booking.type === "room" ? (
                  <>
                    <DoorOpen className="h-5 w-5 text-primary" />
                    Informasi Ruangan
                  </>
                ) : (
                  <>
                    <Package className="h-5 w-5 text-primary" />
                    Informasi Barang
                  </>
                )}
              </h3>
              <div className="space-y-3">
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">
                    Nama {booking.type === "room" ? "Ruangan" : "Barang"}
                  </span>
                  <span className="font-semibold">{booking.itemName}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Tipe Peminjaman</span>
                  <Badge variant="outline">{booking.type === "room" ? "Ruangan" : "Barang"}</Badge>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="pt-6">
              <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                <Clock className="h-5 w-5 text-primary" />
                Jadwal Peminjaman
              </h3>
              <div className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <div className="rounded-lg border bg-background p-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground mb-2">
                      <Calendar className="h-4 w-4" />
                      Waktu Mulai
                    </div>
                    <p className="text-lg font-semibold">{safeFormatDate(booking.startDate, "dd MMMM yyyy")}</p>
                    <p className="text-sm text-muted-foreground">{safeFormatDate(booking.startDate, "HH:mm")} WIB</p>
                  </div>
                  <div className="rounded-lg border bg-background p-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground mb-2">
                      <Calendar className="h-4 w-4" />
                      Waktu Selesai
                    </div>
                    <p className="text-lg font-semibold">{safeFormatDate(booking.endDate, "dd MMMM yyyy")}</p>
                    <p className="text-sm text-muted-foreground">{safeFormatDate(booking.endDate, "HH:mm")} WIB</p>
                  </div>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-primary/10 p-4">
                  <span className="flex items-center gap-2 text-sm font-medium">
                    <Timer className="h-4 w-4" />
                    Durasi Peminjaman
                  </span>
                  <span className="text-lg font-bold">{calculateDuration()}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="pt-6">
              <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                <FileText className="h-5 w-5 text-primary" />
                Tujuan Peminjaman
              </h3>
              <p className="rounded-lg bg-muted p-4 text-sm leading-relaxed">{booking.purpose}</p>
            </CardContent>
          </Card>

          {booking.notes && (
            <Card>
              <CardContent className="pt-6">
                <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                  <FileText className="h-5 w-5 text-primary" />
                  Catatan Tambahan
                </h3>
                <p className="rounded-lg bg-muted p-4 text-sm leading-relaxed">{booking.notes}</p>
              </CardContent>
            </Card>
          )}

          <Card>
            <CardContent className="pt-6">
              <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                <User className="h-5 w-5 text-primary" />
                Informasi Peminjam
              </h3>
              <div className="space-y-3">
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Nama Peminjam</span>
                  <span className="font-semibold">{booking.userName}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Tanggal Pengajuan</span>
                  <span className="font-semibold">{safeFormatDate(booking.createdAt, "dd MMMM yyyy, HH:mm")} WIB</span>
                </div>
              </div>
            </CardContent>
          </Card>

<<<<<<< HEAD
          {booking.status === "approved" && booking.approvedAt && (
=======
          {status === "approved" && booking.approvedAt && (
>>>>>>> 95064e54 (init: setup project and add authorization checks)
            <Card className="border-2 border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950">
              <CardContent className="pt-6">
                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                    <CheckCircle className="h-6 w-6 text-green-600 dark:text-green-400" />
                  </div>
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold text-green-900 dark:text-green-100">Peminjaman Disetujui</h3>
                    <p className="mt-2 text-sm text-green-800 dark:text-green-200">
                      Disetujui oleh Admin pada {safeFormatDate(booking.approvedAt, "dd MMMM yyyy, HH:mm")} WIB
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}

<<<<<<< HEAD
          {booking.status === "rejected" && booking.rejectionReason && (
=======
          {status === "rejected" && booking.rejectionReason && (
>>>>>>> 95064e54 (init: setup project and add authorization checks)
            <Card className="border-2 border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950">
              <CardContent className="pt-6">
                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                    <XCircle className="h-6 w-6 text-red-600 dark:text-red-400" />
                  </div>
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold text-red-900 dark:text-red-100">Peminjaman Ditolak</h3>
                    <p className="mt-2 text-sm font-medium text-red-800 dark:text-red-200">Alasan Penolakan:</p>
                    <p className="mt-1 rounded-lg bg-red-100 p-3 text-sm text-red-900 dark:bg-red-900/50 dark:text-red-100">
                      {booking.rejectionReason}
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}
        </div>
      </DialogContent>
    </Dialog>
  )
}
