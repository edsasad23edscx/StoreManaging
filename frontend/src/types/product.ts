export interface Category {
  id: number
  name: string
  slug: string
  created_at?: string
  updated_at?: string
}

export interface Product {
  id: number
  name: string
  description: string
  category_id: number
  category?: Category
  price: number
  stock_quantity: number
  minimum_stock: number
  image?: string
  created_at?: string
  updated_at?: string
}
