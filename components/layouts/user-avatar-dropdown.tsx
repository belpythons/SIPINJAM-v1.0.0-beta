"use client"

import { Settings, Moon, Sun, LogOut } from "lucide-react"
import { useRouter } from "next/navigation"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { useTheme } from "@/components/providers/theme-provider"
<<<<<<< HEAD
=======
import { logoutAction } from "@/app/actions/auth"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

interface UserAvatarDropdownProps {
  user: {
    name: string
    email: string
    role: "user" | "admin"
  }
}

export function UserAvatarDropdown({ user }: UserAvatarDropdownProps) {
  const router = useRouter()
  const { theme, toggleTheme } = useTheme()

<<<<<<< HEAD
  const handleLogout = () => {
    localStorage.removeItem("sipinjam_auth")
    router.push("/login")
=======
  const handleLogout = async () => {
    await logoutAction()
    router.push("/login")
    router.refresh()
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  }

  const handleSettings = () => {
    const basePath = user.role === "admin" ? "/admin" : "/user"
    router.push(`${basePath}/settings`)
  }

  const getInitials = (name: string) => {
    return name
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .slice(0, 2)
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <button className="flex items-center gap-2 rounded-lg hover:bg-white/10 p-2 transition-colors">
          <Avatar className="h-8 w-8 border-2 border-white/20">
            <AvatarFallback className="bg-white/20 text-white font-semibold text-sm">
              {getInitials(user.name)}
            </AvatarFallback>
          </Avatar>
          <div className="text-left hidden md:block">
            <p className="text-sm font-medium text-white">{user.name}</p>
            <p className="text-xs text-white/70">{user.email}</p>
          </div>
        </button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-56">
        <DropdownMenuLabel>
          <div className="flex flex-col space-y-1">
            <p className="text-sm font-medium">{user.name}</p>
            <p className="text-xs text-muted-foreground">{user.email}</p>
          </div>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuItem onClick={handleSettings}>
          <Settings className="mr-2 h-4 w-4" />
          <span>Settings</span>
        </DropdownMenuItem>
        <DropdownMenuItem onClick={toggleTheme}>
          {theme === "light" ? <Moon className="mr-2 h-4 w-4" /> : <Sun className="mr-2 h-4 w-4" />}
          <span>{theme === "light" ? "Dark Mode" : "Light Mode"}</span>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem onClick={handleLogout} className="text-destructive">
          <LogOut className="mr-2 h-4 w-4" />
          <span>Logout</span>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
