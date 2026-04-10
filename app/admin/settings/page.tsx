"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
<<<<<<< HEAD
import { getUserSession, setUserSession } from "@/lib/auth"
import { AccountSettings } from "@/components/settings/account-settings"
import type { User } from "@/lib/types"
=======
import { getCurrentUserAction } from "@/app/actions/auth"
import { updateUserProfile } from "@/app/actions/users"
import { AccountSettings } from "@/components/settings/account-settings"
import type { User } from "@/lib/types"
import { toast } from "sonner"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

export default function AdminSettingsPage() {
  const router = useRouter()
  const [user, setUser] = useState<User | null>(null)

  useEffect(() => {
<<<<<<< HEAD
    const currentUser = getUserSession()
    if (!currentUser || currentUser.role !== "admin") {
      router.push("/login")
      return
    }
    setUser(currentUser)
  }, [router])

  const handleUpdateUser = (updatedUser: User) => {
    setUserSession(updatedUser)
    setUser(updatedUser)
=======
    const fetchUser = async () => {
      const currentUser = await getCurrentUserAction()
      if (!currentUser || currentUser.role !== "admin") {
        router.push("/login")
        return
      }
      setUser(currentUser)
    }
    fetchUser()
  }, [router])

  const handleUpdateUser = async (updatedUser: User) => {
    try {
      const result = await updateUserProfile(updatedUser.id, {
        name: updatedUser.name,
        email: updatedUser.email
      })

      if (result.success) {
        setUser(updatedUser)
        toast.success("Profil berhasil diperbarui")
      } else {
        toast.error(result.error || "Gagal memperbarui profil")
      }
    } catch (error) {
      toast.error("Terjadi kesalahan sistem")
    }
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  if (!user) {
    return null
  }

  return (
    <div className="p-6">
      <div className="max-w-3xl">
        <h1 className="text-3xl font-bold mb-2">Settings</h1>
        <p className="text-muted-foreground mb-6">Kelola informasi akun dan preferensi Anda</p>

        <AccountSettings user={user} onUpdate={handleUpdateUser} />
      </div>
    </div>
  )
}
