"use server"

import { prisma } from "@/lib/prisma"
import bcrypt from "bcryptjs"
import { cookies } from "next/headers"
import type { User, LoginCredentials } from "@/lib/types"

const SESSION_COOKIE_NAME = "sipinjam_session"

export async function authenticateUserAction(
  credentials: LoginCredentials,
): Promise<{ success: boolean; user?: User; error?: string }> {
  try {
    const user = await prisma.user.findUnique({
      where: { email: credentials.email },
    })

    if (!user) {
      return { success: false, error: "Email tidak ditemukan" }
    }

    if (user.role.toLowerCase() !== credentials.role.toLowerCase()) {
      return { success: false, error: "Role tidak sesuai" }
    }

    if (!user.isActive) {
      return { success: false, error: "Akun Anda telah dinonaktifkan" }
    }

    const isPasswordValid = await bcrypt.compare(credentials.password, user.password)

    if (!isPasswordValid) {
      return { success: false, error: "Password salah" }
    }

    const appUser: User = {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role.toLowerCase() as any,
      isActive: user.isActive,
      createdAt: user.createdAt,
    }

    // Set session cookie
    const cookieStore = await cookies()
    cookieStore.set(SESSION_COOKIE_NAME, JSON.stringify(appUser), {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      sameSite: "lax",
      maxAge: 60 * 60 * 24 * 7, // 1 week
      path: "/",
    })

    return { success: true, user: appUser }
  } catch (error) {
    console.error("Authentication error:", error)
    return { success: false, error: "Terjadi kesalahan sistem" }
  }
}

export async function authenticateWithGoogleAction(
  role: "user" | "admin",
): Promise<{ success: boolean; user?: User; error?: string }> {
  await new Promise((resolve) => setTimeout(resolve, 1500))

  try {
    const user = await prisma.user.findFirst({
      where: { role: role.toUpperCase() },
    })

    if (user) {
      const appUser: User = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role.toLowerCase() as any,
        isActive: user.isActive,
        createdAt: user.createdAt,
      }

      // Set session cookie
      const cookieStore = await cookies()
      cookieStore.set(SESSION_COOKIE_NAME, JSON.stringify(appUser), {
        httpOnly: true,
        secure: process.env.NODE_ENV === "production",
        sameSite: "lax",
        maxAge: 60 * 60 * 24 * 7, // 1 week
        path: "/",
      })

      return { success: true, user: appUser }
    }

    return { success: false, error: "Google login gagal" }
  } catch (error) {
    return { success: false, error: "Terjadi kesalahan sistem" }
  }
}

export async function logoutAction() {
  const cookieStore = await cookies()
  cookieStore.delete(SESSION_COOKIE_NAME)
  return { success: true }
}

export async function getCurrentUserAction(): Promise<User | null> {
  const cookieStore = await cookies()
  const session = cookieStore.get(SESSION_COOKIE_NAME)

  if (!session) return null

  try {
    return JSON.parse(session.value) as User
  } catch (error) {
    return null
  }
}
