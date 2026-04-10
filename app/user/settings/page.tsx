"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
<<<<<<< HEAD
import { getUserSession, setUserSession } from "@/lib/auth"
=======
import { getCurrentUserAction } from "@/app/actions/auth"
>>>>>>> 95064e54 (init: setup project and add authorization checks)
import { AccountSettings } from "@/components/settings/account-settings"
import type { User } from "@/lib/types"

export default function UserSettingsPage() {
  const router = useRouter()
  const [user, setUser] = useState<User | null>(null)

  useEffect(() => {
<<<<<<< HEAD
    const currentUser = getUserSession()
    if (!currentUser || currentUser.role !== "user") {
      router.push("/login")
      return
    }
    setUser(currentUser)
  }, [router])

  const handleUpdateUser = (updatedUser: User) => {
    setUserSession(updatedUser)
=======
    const checkUser = async () => {
      const currentUser = await getCurrentUserAction()
      if (!currentUser || currentUser.role !== "user") {
        router.push("/login")
        return
      }
      setUser(currentUser)
    }
    checkUser()
  }, [router])

  const handleUpdateUser = (updatedUser: User) => {
    // Note: In a real app, this should call a server action to update the DB and cookie
>>>>>>> 95064e54 (init: setup project and add authorization checks)
    setUser(updatedUser)
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
