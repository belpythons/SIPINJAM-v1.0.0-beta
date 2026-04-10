<<<<<<< HEAD
import { AlertCircle, Clock, UserCheck, Shield, XCircle, AlertTriangle } from "lucide-react"
import { mockRules } from "@/lib/mock-data"
=======
"use client"

import { useState, useEffect } from "react"
import { AlertCircle, Clock, UserCheck, Shield, XCircle, AlertTriangle } from "lucide-react"
import { fetchRules } from "@/app/actions/assets"
import type { Rule } from "@/lib/types"
>>>>>>> 95064e54 (init: setup project and add authorization checks)

const ruleIcons = [Clock, UserCheck, AlertCircle, Shield, XCircle, AlertTriangle]

export function RulesList() {
<<<<<<< HEAD
  return (
    <div className="space-y-4">
      {mockRules.map((rule, index) => {
        const Icon = ruleIcons[index] || AlertCircle
=======
  const [rules, setRules] = useState<Rule[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const loadRules = async () => {
      try {
        const data = await fetchRules()
        setRules(data as any)
      } catch (error) {
        console.error("Failed to load rules:", error)
      } finally {
        setLoading(false)
      }
    }
    loadRules()
  }, [])

  if (loading) {
    return (
      <div className="space-y-4">
        {[1, 2, 3].map((i) => (
          <div key={i} className="h-32 w-full animate-pulse rounded-lg border bg-muted" />
        ))}
      </div>
    )
  }

  if (rules.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-12 text-center">
        <p className="text-muted-foreground">Belum ada peraturan yang tersedia.</p>
      </div>
    )
  }

  return (
    <div className="space-y-4">
      {rules.map((rule, index) => {
        const Icon = ruleIcons[index % ruleIcons.length] || AlertCircle
>>>>>>> 95064e54 (init: setup project and add authorization checks)
        return (
          <div key={rule.id} className="rounded-lg border bg-card p-6 transition-all hover:shadow-md">
            <div className="flex items-start gap-4">
              <div className="rounded-lg bg-primary/10 p-3 shrink-0">
                <Icon className="h-6 w-6 text-primary" />
              </div>
              <div className="flex-1 space-y-2">
                <div className="flex items-center gap-2">
                  <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">
<<<<<<< HEAD
                    {rule.id}
=======
                    {index + 1}
>>>>>>> 95064e54 (init: setup project and add authorization checks)
                  </span>
                  <h3 className="text-lg font-semibold">{rule.title}</h3>
                </div>
                <p className="text-muted-foreground leading-relaxed">{rule.content}</p>
              </div>
            </div>
          </div>
        )
      })}
    </div>
  )
}
