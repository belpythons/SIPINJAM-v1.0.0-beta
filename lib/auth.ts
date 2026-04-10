<<<<<<< HEAD
import { mockUsers, MOCK_PASSWORD } from "./mock-data"
import type { User, LoginCredentials } from "./types"

// Simulate authentication with mock data
export async function authenticateUser(
  credentials: LoginCredentials,
): Promise<{ success: boolean; user?: User; error?: string }> {
  // Simulate network delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  const user = mockUsers.find((u) => u.email === credentials.email && u.role === credentials.role)

  if (!user) {
    return { success: false, error: "Email atau role tidak ditemukan" }
  }

  if (!user.isActive) {
    return { success: false, error: "Akun Anda telah dinonaktifkan" }
  }

  // Check mock password
  if (credentials.password !== MOCK_PASSWORD) {
    return { success: false, error: "Password salah" }
  }

  return { success: true, user }
}
=======
import type { User } from "./types"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

// Store user session in localStorage
export function setUserSession(user: User): void {
  if (typeof window !== "undefined") {
    localStorage.setItem("sipinjam_auth", JSON.stringify(user))
  }
}

// Get user session from localStorage
export function getUserSession(): User | null {
  if (typeof window !== "undefined") {
    const userStr = localStorage.getItem("sipinjam_auth")
    if (userStr) {
      return JSON.parse(userStr)
    }
  }
  return null
}

// Clear user session
export function clearUserSession(): void {
  if (typeof window !== "undefined") {
    localStorage.removeItem("sipinjam_auth")
  }
}
<<<<<<< HEAD

// Simulate Google OAuth
export async function authenticateWithGoogle(
  role: "user" | "admin",
): Promise<{ success: boolean; user?: User; error?: string }> {
  await new Promise((resolve) => setTimeout(resolve, 1500))

  // Mock Google login - return the first user of the selected role
  const user = mockUsers.find((u) => u.role === role)

  if (user) {
    return { success: true, user }
  }

  return { success: false, error: "Google login gagal" }
}
=======
>>>>>>> 95064e54 (init: setup project and add authorization checks)
