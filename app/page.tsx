<<<<<<< HEAD
"use client"

import { useEffect } from "react"
import { useRouter } from "next/navigation"
import { Loader2 } from "lucide-react"

export default function Home() {
  const router = useRouter()

  useEffect(() => {
    // Immediate redirect to login
    router.replace("/login")
  }, [router])

  // Show loading state while redirecting
  return (
    <div className="flex min-h-screen items-center justify-center">
      <div className="flex flex-col items-center gap-4">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <p className="text-sm text-muted-foreground">Mengarahkan ke halaman login...</p>
      </div>
    </div>
  )
=======
import { redirect } from "next/navigation"

export default function Home() {
  redirect("/login")
>>>>>>> 95064e54 (init: setup project and add authorization checks)
}
