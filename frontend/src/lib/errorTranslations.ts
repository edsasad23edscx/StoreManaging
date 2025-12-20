const fieldNameMap: Record<string, string> = {
  name: 'Nazwa produktu',
  description: 'Opis',
  price: 'Cena',
  stock_quantity: 'Ilość w magazynie',
  minimum_stock: 'Minimalna ilość',
  category_id: 'Kategoria',
  image: 'Obraz',
  email: 'Email',
  password: 'Hasło',
}

const errorMessageMap: Record<string, string> = {
  required: 'jest wymagane',
  numeric: 'musi być liczbą',
  integer: 'musi być liczbą całkowitą',
  exists: 'jest nieprawidłowe',
  email: 'musi być poprawnym adresem email',
  unique: 'już istnieje',
  min: 'jest za małe',
  max: 'jest za duże',
  image: 'musi być obrazem',
}

export const translateError = (error: string): string => {
  // Error format from Laravel: "The field_name field is rule_name"
  let result = error

  // Remove "The" and "field is" wrapper text from Laravel
  result = result.replace(/^The\s+/i, '')
  result = result.replace(/\s+field\s+is\s+/, ' ')

  // Capitalize first letter
  result = result.charAt(0).toUpperCase() + result.slice(1)

  // Try to translate field names
  for (const [field, polish] of Object.entries(fieldNameMap)) {
    result = result.replace(new RegExp(`\\b${field}\\b`, 'gi'), polish)
  }

  // Try to translate common error patterns
  for (const [rule, polish] of Object.entries(errorMessageMap)) {
    result = result.replace(new RegExp(`\\b${rule}\\b`, 'gi'), polish)
  }

  return result
}

export const translateErrors = (errors: Record<string, string[]>): string[] => {
  return Object.entries(errors).flatMap(([_, messages]) => {
    if (Array.isArray(messages)) {
      return messages.map((msg) => translateError(msg))
    }
    return [translateError(messages as string)]
  })
}
