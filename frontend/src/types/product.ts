export interface Product {
  id: number
  name: string
  description: string
  category: string
  price: number
  stock_quantity: number
  minimum_stock: number
  image?: string
  created_at?: string
  updated_at?: string
}
