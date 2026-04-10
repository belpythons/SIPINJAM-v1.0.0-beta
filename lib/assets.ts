import { prisma } from "./prisma"

// Room CRUD
export async function getRooms() {
  return await prisma.room.findMany({
    orderBy: { name: "asc" },
  })
}

export async function getRoomById(id: string) {
  return await prisma.room.findUnique({
    where: { id },
  })
}

export async function createRoom(data: {
  name: string
  capacity: number
  description?: string
  imageUrl?: string
  building?: string
  floor?: number
}) {
  return await prisma.room.create({
    data,
  })
}

export async function updateRoom(id: string, data: Partial<{
  name: string
  capacity: number
  description?: string
  imageUrl?: string
  building?: string
  floor?: number
}>) {
  return await prisma.room.update({
    where: { id },
    data,
  })
}

export async function deleteRoom(id: string) {
  return await prisma.room.delete({
    where: { id },
  })
}

// Equipment CRUD
export async function getEquipment() {
  return await prisma.equipment.findMany({
    orderBy: { name: "asc" },
  })
}

export async function getEquipmentById(id: string) {
  return await prisma.equipment.findUnique({
    where: { id },
  })
}

export async function createEquipment(data: {
  name: string
  category: string
  quantity: number
  available: number
  description?: string
  imageUrl?: string
}) {
  return await prisma.equipment.create({
    data,
  })
}

export async function updateEquipment(id: string, data: Partial<{
  name: string
  category: string
  quantity: number
  available: number
  description?: string
  imageUrl?: string
}>) {
  return await prisma.equipment.update({
    where: { id },
    data,
  })
}

export async function deleteEquipment(id: string) {
  return await prisma.equipment.delete({
    where: { id },
  })
}
