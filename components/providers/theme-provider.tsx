"use client"

import * as React from "react"

type Theme = "light" | "dark"

interface ThemeContextType {
  theme: Theme
  toggleTheme: () => void
}

const ThemeContext = React.createContext<ThemeContextType | undefined>(undefined)

<<<<<<< HEAD
export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [theme, setTheme] = React.useState<Theme>("light")
  const [mounted, setMounted] = React.useState(false)

  React.useEffect(() => {
    setMounted(true)
=======
export function ThemeProvider({ children, attribute, defaultTheme, enableSystem, disableTransitionOnChange }: { 
  children: React.ReactNode,
  attribute?: string,
  defaultTheme?: string,
  enableSystem?: boolean,
  disableTransitionOnChange?: boolean
}) {
  const [theme, setTheme] = React.useState<Theme>("light")

  React.useEffect(() => {
>>>>>>> 95064e54 (init: setup project and add authorization checks)
    const stored = localStorage.getItem("theme") as Theme
    if (stored) {
      setTheme(stored)
      document.documentElement.classList.toggle("dark", stored === "dark")
<<<<<<< HEAD
    }
  }, [])
=======
    } else if (defaultTheme === "dark") {
      setTheme("dark")
      document.documentElement.classList.add("dark")
    }
  }, [defaultTheme])
>>>>>>> 95064e54 (init: setup project and add authorization checks)

  const toggleTheme = React.useCallback(() => {
    setTheme((prev) => {
      const next = prev === "light" ? "dark" : "light"
      localStorage.setItem("theme", next)
      document.documentElement.classList.toggle("dark", next === "dark")
      return next
    })
  }, [])

<<<<<<< HEAD
  if (!mounted) {
    return <>{children}</>
  }

=======
>>>>>>> 95064e54 (init: setup project and add authorization checks)
  return <ThemeContext.Provider value={{ theme, toggleTheme }}>{children}</ThemeContext.Provider>
}

export function useTheme() {
  const context = React.useContext(ThemeContext)
  if (!context) {
    throw new Error("useTheme must be used within ThemeProvider")
  }
  return context
}
