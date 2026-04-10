"use client"

import type React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Checkbox } from "@/components/ui/checkbox"
import { Eye, EyeOff, Loader2 } from "lucide-react"
import type { UserRole } from "@/lib/types"
<<<<<<< HEAD
import { authenticateUser, setUserSession, authenticateWithGoogle } from "@/lib/auth"
=======
import { authenticateUserAction, authenticateWithGoogleAction } from "@/app/actions/auth"
>>>>>>> 95064e54 (init: setup project and add authorization checks)
import { useToast } from "@/hooks/use-toast"
import { ForgotPasswordModal } from "./forgot-password-modal"

interface LoginFormProps {
  selectedRole: UserRole
  onRoleChange: (role: UserRole) => void
}

export function LoginForm({ selectedRole, onRoleChange }: LoginFormProps) {
  const router = useRouter()
  const { toast } = useToast()
  const [showPassword, setShowPassword] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [isGoogleLoading, setIsGoogleLoading] = useState(false)
  const [showForgotPassword, setShowForgotPassword] = useState(false)
  const [rememberMe, setRememberMe] = useState(false)
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  })
  const [errors, setErrors] = useState({
    email: "",
    password: "",
  })

  // Real-time validation
  const validateEmail = (email: string) => {
    if (!email) return "Email wajib diisi"
    if (!/\S+@\S+\.\S+/.test(email)) return "Format email tidak valid"
    return ""
  }

  const validatePassword = (password: string) => {
    if (!password) return "Password wajib diisi"
    if (password.length < 6) return "Password minimal 6 karakter"
    return ""
  }

  const handleEmailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const email = e.target.value
    setFormData((prev) => ({ ...prev, email }))
    setErrors((prev) => ({ ...prev, email: validateEmail(email) }))
  }

  const handlePasswordChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const password = e.target.value
    setFormData((prev) => ({ ...prev, password }))
    setErrors((prev) => ({ ...prev, password: validatePassword(password) }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    // Validate all fields
    const emailError = validateEmail(formData.email)
    const passwordError = validatePassword(formData.password)

    setErrors({
      email: emailError,
      password: passwordError,
    })

    if (emailError || passwordError) {
      return
    }

    setIsLoading(true)

    try {
<<<<<<< HEAD
      const result = await authenticateUser({
=======
      const result = await authenticateUserAction({
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        email: formData.email,
        password: formData.password,
        role: selectedRole,
      })

      if (result.success && result.user) {
        if (rememberMe) {
          localStorage.setItem("rememberMe", "true")
          localStorage.setItem("rememberedEmail", formData.email)
        } else {
          localStorage.removeItem("rememberMe")
          localStorage.removeItem("rememberedEmail")
        }

<<<<<<< HEAD
        setUserSession(result.user)
=======
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        toast({
          title: "Login Berhasil",
          description: `Selamat datang, ${result.user.name}!`,
        })

        // Redirect based on role
        if (result.user.role === "admin") {
<<<<<<< HEAD
          router.push("/admin/dashboard")
        } else {
          router.push("/user/dashboard")
=======
          window.location.href = "/admin/dashboard"
        } else {
          window.location.href = "/user/dashboard"
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        }
      } else {
        toast({
          title: "Login Gagal",
          description: result.error || "Terjadi kesalahan",
          variant: "destructive",
        })
      }
    } catch (error) {
      toast({
        title: "Error",
        description: "Terjadi kesalahan sistem",
        variant: "destructive",
      })
    } finally {
      setIsLoading(false)
    }
  }

  const handleGoogleLogin = async () => {
    setIsGoogleLoading(true)

    try {
<<<<<<< HEAD
      const result = await authenticateWithGoogle(selectedRole)

      if (result.success && result.user) {
        setUserSession(result.user)
=======
      const result = await authenticateWithGoogleAction(selectedRole)
      if (result.success && result.user) {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        toast({
          title: "Login Berhasil",
          description: `Selamat datang, ${result.user.name}!`,
        })

        // Redirect based on role
        if (result.user.role === "admin") {
<<<<<<< HEAD
          router.push("/admin/dashboard")
        } else {
          router.push("/user/dashboard")
=======
          window.location.href = "/admin/dashboard"
        } else {
          window.location.href = "/user/dashboard"
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        }
      } else {
        toast({
          title: "Login Gagal",
          description: result.error || "Terjadi kesalahan",
          variant: "destructive",
        })
      }
    } catch (error) {
      toast({
        title: "Error",
        description: "Terjadi kesalahan sistem",
        variant: "destructive",
      })
    } finally {
      setIsGoogleLoading(false)
    }
  }

  const themeColor = selectedRole === "user" ? "blue" : "orange"

  return (
    <>
      <div className="space-y-6">
        <div className="space-y-2 text-center">
          <h2 className="text-3xl font-bold tracking-tight">Masuk ke SIPINJAM</h2>
          <p className="text-muted-foreground">Masukkan email dan password Anda untuk melanjutkan</p>
        </div>

        {/* Role Selection Tabs */}
        <Tabs value={selectedRole} onValueChange={(value) => onRoleChange(value as UserRole)} className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="user" className="data-[state=active]:bg-blue-600 data-[state=active]:text-white">
              User
            </TabsTrigger>
            <TabsTrigger value="admin" className="data-[state=active]:bg-orange-600 data-[state=active]:text-white">
              Admin
            </TabsTrigger>
          </TabsList>
        </Tabs>

        {/* Login Form */}
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="email">Email</Label>
            <Input
              id="email"
              type="email"
              placeholder="nama@sipinjam.ac.id"
              value={formData.email}
              onChange={handleEmailChange}
              className={errors.email ? "border-destructive" : ""}
              disabled={isLoading}
            />
            {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
          </div>

          <div className="space-y-2">
            <Label htmlFor="password">Password</Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                placeholder="Masukkan password"
                value={formData.password}
                onChange={handlePasswordChange}
                className={errors.password ? "border-destructive pr-10" : "pr-10"}
                disabled={isLoading}
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                disabled={isLoading}
              >
                {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
              </button>
            </div>
            {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
          </div>

          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <Checkbox
                id="remember"
                checked={rememberMe}
                onCheckedChange={(checked) => setRememberMe(checked as boolean)}
                disabled={isLoading}
              />
              <label
                htmlFor="remember"
                className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                Ingat Saya
              </label>
            </div>
            <button
              type="button"
              onClick={() => setShowForgotPassword(true)}
              className={`text-sm font-medium transition-colors ${
                themeColor === "blue" ? "text-blue-600 hover:text-blue-700" : "text-orange-600 hover:text-orange-700"
              }`}
            >
              Lupa Password?
            </button>
          </div>

          <Button
            type="submit"
            className={`w-full transition-colors ${
              themeColor === "blue" ? "bg-blue-600 hover:bg-blue-700" : "bg-orange-600 hover:bg-orange-700"
            }`}
            disabled={isLoading}
          >
            {isLoading ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Memproses...
              </>
            ) : (
              "Masuk"
            )}
          </Button>
        </form>

        {/* Divider */}
        <div className="relative">
          <div className="absolute inset-0 flex items-center">
            <div className="w-full border-t" />
          </div>
          <div className="relative flex justify-center text-xs uppercase">
            <span className="bg-background px-2 text-muted-foreground">Atau lanjutkan dengan</span>
          </div>
        </div>

        {/* Google Sign In */}
        <Button
          type="button"
          variant="outline"
          className="w-full bg-transparent"
          onClick={handleGoogleLogin}
          disabled={isGoogleLoading}
        >
          {isGoogleLoading ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Memproses...
            </>
          ) : (
            <>
              <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
                <path
                  fill="currentColor"
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                  fill="currentColor"
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                  fill="currentColor"
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                />
                <path
                  fill="currentColor"
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                />
              </svg>
              Masuk dengan Google
            </>
          )}
        </Button>

        {/* Demo Credentials Info */}
        <div className="rounded-lg bg-muted p-4 text-sm">
          <p className="font-medium mb-2">Demo Credentials:</p>
          <div className="space-y-1 text-muted-foreground">
            <p>User: user@sipinjam.ac.id</p>
            <p>Admin: admin@sipinjam.ac.id</p>
            <p>Password: password123</p>
          </div>
        </div>
      </div>

      <ForgotPasswordModal open={showForgotPassword} onOpenChange={setShowForgotPassword} />
    </>
  )
}
