"use client"

import { useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { toast } from "sonner"
import { Mail, Lock, Eye, EyeOff } from "lucide-react"
import type { User } from "@/lib/types"

const emailSchema = z
  .object({
    currentEmail: z.string().email("Email tidak valid"),
    newEmail: z.string().email("Email tidak valid"),
    confirmEmail: z.string().email("Email tidak valid"),
  })
  .refine((data) => data.newEmail === data.confirmEmail, {
    message: "Email tidak cocok",
    path: ["confirmEmail"],
  })

const passwordSchema = z
  .object({
    currentPassword: z.string().min(6, "Password minimal 6 karakter"),
    newPassword: z.string().min(6, "Password minimal 6 karakter"),
    confirmPassword: z.string().min(6, "Password minimal 6 karakter"),
  })
  .refine((data) => data.newPassword === data.confirmPassword, {
    message: "Password tidak cocok",
    path: ["confirmPassword"],
  })

interface AccountSettingsProps {
  user: User
  onUpdate: (updatedUser: User) => void
}

export function AccountSettings({ user, onUpdate }: AccountSettingsProps) {
  const [showCurrentPassword, setShowCurrentPassword] = useState(false)
  const [showNewPassword, setShowNewPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)
  const [isUpdatingEmail, setIsUpdatingEmail] = useState(false)
  const [isUpdatingPassword, setIsUpdatingPassword] = useState(false)

  const emailForm = useForm({
    resolver: zodResolver(emailSchema),
    defaultValues: {
      currentEmail: user.email,
      newEmail: "",
      confirmEmail: "",
    },
  })

  const passwordForm = useForm({
    resolver: zodResolver(passwordSchema),
    defaultValues: {
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
    },
  })

  const onEmailSubmit = async (data: z.infer<typeof emailSchema>) => {
    setIsUpdatingEmail(true)

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500))

    // Update user email in localStorage
    const updatedUser = { ...user, email: data.newEmail }
    onUpdate(updatedUser)

    toast.success("Email berhasil diubah", {
      description: `Email baru: ${data.newEmail}`,
    })

    emailForm.reset({
      currentEmail: data.newEmail,
      newEmail: "",
      confirmEmail: "",
    })

    setIsUpdatingEmail(false)
  }

  const onPasswordSubmit = async (data: z.infer<typeof passwordSchema>) => {
    setIsUpdatingPassword(true)

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500))

    toast.success("Password berhasil diubah", {
      description: "Silakan gunakan password baru untuk login berikutnya",
    })

    passwordForm.reset()
    setIsUpdatingPassword(false)
  }

  return (
    <div className="space-y-6">
      {/* Change Email */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Mail className="h-5 w-5" />
            Ubah Email
          </CardTitle>
          <CardDescription>Ubah alamat email yang digunakan untuk login</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={emailForm.handleSubmit(onEmailSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="currentEmail">Email Saat Ini</Label>
              <Input id="currentEmail" type="email" disabled {...emailForm.register("currentEmail")} />
            </div>

            <div className="space-y-2">
              <Label htmlFor="newEmail">Email Baru</Label>
              <Input
                id="newEmail"
                type="email"
                placeholder="emailbaru@example.com"
                {...emailForm.register("newEmail")}
              />
              {emailForm.formState.errors.newEmail && (
                <p className="text-sm text-destructive">{emailForm.formState.errors.newEmail.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="confirmEmail">Konfirmasi Email Baru</Label>
              <Input
                id="confirmEmail"
                type="email"
                placeholder="emailbaru@example.com"
                {...emailForm.register("confirmEmail")}
              />
              {emailForm.formState.errors.confirmEmail && (
                <p className="text-sm text-destructive">{emailForm.formState.errors.confirmEmail.message}</p>
              )}
            </div>

            <Button type="submit" disabled={isUpdatingEmail}>
              {isUpdatingEmail ? "Mengubah..." : "Ubah Email"}
            </Button>
          </form>
        </CardContent>
      </Card>

      {/* Change Password */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Lock className="h-5 w-5" />
            Ubah Password
          </CardTitle>
          <CardDescription>Ubah password yang digunakan untuk login</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={passwordForm.handleSubmit(onPasswordSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="currentPassword">Password Saat Ini</Label>
              <div className="relative">
                <Input
                  id="currentPassword"
                  type={showCurrentPassword ? "text" : "password"}
                  placeholder="••••••••"
                  {...passwordForm.register("currentPassword")}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => setShowCurrentPassword(!showCurrentPassword)}
                >
                  {showCurrentPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </Button>
              </div>
              {passwordForm.formState.errors.currentPassword && (
                <p className="text-sm text-destructive">{passwordForm.formState.errors.currentPassword.message}</p>
              )}
            </div>

            <div className="space-y-2">
<<<<<<< HEAD
              <Label htmlId="newPassword">Password Baru</Label>
=======
              <Label htmlFor="newPassword">Password Baru</Label>
>>>>>>> 95064e54 (init: setup project and add authorization checks)
              <div className="relative">
                <Input
                  id="newPassword"
                  type={showNewPassword ? "text" : "password"}
                  placeholder="••••••••"
                  {...passwordForm.register("newPassword")}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => setShowNewPassword(!showNewPassword)}
                >
                  {showNewPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </Button>
              </div>
              {passwordForm.formState.errors.newPassword && (
                <p className="text-sm text-destructive">{passwordForm.formState.errors.newPassword.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="confirmPassword">Konfirmasi Password Baru</Label>
              <div className="relative">
                <Input
                  id="confirmPassword"
                  type={showConfirmPassword ? "text" : "password"}
                  placeholder="••••••••"
                  {...passwordForm.register("confirmPassword")}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                >
                  {showConfirmPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </Button>
              </div>
              {passwordForm.formState.errors.confirmPassword && (
                <p className="text-sm text-destructive">{passwordForm.formState.errors.confirmPassword.message}</p>
              )}
            </div>

            <Button type="submit" disabled={isUpdatingPassword}>
              {isUpdatingPassword ? "Mengubah..." : "Ubah Password"}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
