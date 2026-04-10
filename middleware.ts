import { NextResponse } from "next/server"
import type { NextRequest } from "next/server"

const SESSION_COOKIE_NAME = "sipinjam_session"

export function middleware(request: NextRequest) {
  const session = request.cookies.get(SESSION_COOKIE_NAME)
  const { pathname } = request.nextUrl

  // Define protected routes
  const isAdminRoute = pathname.startsWith("/admin")
  const isUserRoute = pathname.startsWith("/user")
  const isLoginRoute = pathname === "/login"

  // If user is trying to access protected routes but has no session
  if ((isAdminRoute || isUserRoute) && !session) {
    return NextResponse.redirect(new URL("/login", request.url))
  }

  // If user has session and is trying to access login page
  if (isLoginRoute && session) {
    try {
      const user = JSON.parse(session.value)
      if (user.role === "admin") {
        return NextResponse.redirect(new URL("/admin/dashboard", request.url))
      } else {
        return NextResponse.redirect(new URL("/user/dashboard", request.url))
      }
    } catch (error) {
      // If cookie is invalid, delete it and allow login
      const response = NextResponse.next()
      response.cookies.delete(SESSION_COOKIE_NAME)
      return response
    }
  }

  // Role-based access control
  if (session) {
    try {
      const user = JSON.parse(session.value)
      
      if (isAdminRoute && user.role !== "admin") {
        return NextResponse.redirect(new URL("/user/dashboard", request.url))
      }
      
      if (isUserRoute && user.role !== "user") {
        return NextResponse.redirect(new URL("/admin/dashboard", request.url))
      }
    } catch (error) {
      const response = NextResponse.redirect(new URL("/login", request.url))
      response.cookies.delete(SESSION_COOKIE_NAME)
      return response
    }
  }

  return NextResponse.next()
}

export const config = {
  matcher: [
    /*
     * Match all request paths except for the ones starting with:
     * - api (API routes)
     * - _next/static (static files)
     * - _next/image (image optimization files)
     * - favicon.ico (favicon file)
     * - public (public folder)
     */
    "/((?!api|_next/static|_next/image|favicon.ico|public).*)",
  ],
}
