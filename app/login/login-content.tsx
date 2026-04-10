"use client"

import { useState } from "react"
import { LoginForm } from "@/components/auth/login-form"
import { BookCheck } from "lucide-react"
import type { UserRole } from "@/lib/types"
import Image from "next/image"

export function LoginContent() {
  const [selectedRole, setSelectedRole] = useState<UserRole>("user")

  return (
    <div className="min-h-screen flex flex-col lg:flex-row">
      {/* Left Side - Pattern Background with Overlay */}
      <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <div
          className="absolute inset-0"
          style={{
            backgroundImage: `
              linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
              linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px)
            `,
            backgroundSize: "50px 50px",
            backgroundColor: "#f8fafc",
          }}
        />
        {/* Subtle overlay image */}
        <div
          className="absolute inset-0 opacity-20"
          style={{
            backgroundImage: "url(/modern-university-classroom.jpg)",
            backgroundSize: "cover",
            backgroundPosition: "center",
          }}
        />
        <div
          className={`absolute inset-0 transition-all duration-500 ${
            selectedRole === "user"
              ? "bg-gradient-to-br from-blue-900/95 via-blue-800/90 to-blue-900/95"
              : "bg-gradient-to-br from-orange-900/95 via-orange-800/90 to-orange-900/95"
          }`}
        />

        {/* Content Overlay */}
        <div className="relative z-10 flex flex-col items-start justify-between p-12 text-white w-full">
          {/* Replaced icon-based logo with SD logo image */}
          <div className="flex items-center gap-3">
            <div className="p-3 rounded-xl bg-white/10">
              <Image src="/sipinjam-logo.png" alt="SIPINJAM Logo" width={48} height={48} className="object-contain" />
            </div>
            <div>
              <h1 className="text-2xl font-bold">SIPINJAM</h1>
              <p className="text-sm text-white/80">Sistem Informasi Peminjaman</p>
            </div>
          </div>

          <div className="space-y-6 max-w-md">
            <div className="flex items-center gap-3">
              <BookCheck className="w-12 h-12" />
              <h2 className="text-4xl font-bold leading-tight text-balance">Kelola Peminjaman dengan Mudah</h2>
            </div>
            <p className="text-lg text-white/90 leading-relaxed text-pretty">
              Platform terpadu untuk peminjaman ruangan dan barang kampus. Proses cepat, transparan, dan efisien.
            </p>
            <ul className="space-y-3 text-white/90" role="list">
              <li className="flex items-center gap-2">
                <div className="w-1.5 h-1.5 rounded-full bg-white" />
                <span>Booking online 24/7</span>
              </li>
              <li className="flex items-center gap-2">
                <div className="w-1.5 h-1.5 rounded-full bg-white" />
                <span>Tracking status real-time</span>
              </li>
              <li className="flex items-center gap-2">
                <div className="w-1.5 h-1.5 rounded-full bg-white" />
                <span>Riwayat peminjaman lengkap</span>
              </li>
            </ul>
          </div>

          <div className="text-sm text-white/60">© 2024 SIPINJAM. Universitas Indonesia.</div>
        </div>
      </div>

      {/* Right Side - Login Form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-8 bg-background">
        <div className="w-full max-w-md animate-fade-in">
          {/* Replaced mobile icon logo with SD logo image */}
          <div className="lg:hidden flex items-center gap-3 mb-8">
            <div className="p-2 rounded-lg bg-white/10 border border-border">
              <Image src="/sipinjam-logo.png" alt="SIPINJAM Logo" width={32} height={32} className="object-contain" />
            </div>
            <div>
              <h1 className="text-xl font-bold">SIPINJAM</h1>
              <p className="text-xs text-muted-foreground">Sistem Informasi Peminjaman</p>
            </div>
          </div>

          <LoginForm selectedRole={selectedRole} onRoleChange={setSelectedRole} />
        </div>
      </div>
    </div>
  )
}
