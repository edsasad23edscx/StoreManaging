const FIELD_TRANSLATIONS: Record<string, string> = {
  name: 'Nazwa produktu',
  description: 'Opis',
  price: 'Cena',
  stock_quantity: 'Ilość w magazynie',
  minimum_stock: 'Minimalna ilość',
  category_id: 'Kategoria',
  image: 'Obraz',
  email: 'Email',
  password: 'Hasło',
} as const

const ERROR_RULE_TRANSLATIONS: Record<string, string> = {
  required: 'jest wymagane',
  numeric: 'musi być liczbą',
  integer: 'musi być liczbą całkowitą',
  exists: 'jest nieprawidłowe',
  email: 'musi być poprawnym adresem email',
  unique: 'już istnieje',
  min: 'jest za małe',
  max: 'jest za duże',
  image: 'musi być obrazem',
} as const

const cleanLaravelErrorMessage = (error: string): string => {
  return error
    .replace(/^The\s+/i, '')
    .replace(/\s+field\s+is\s+/, ' ')
}

const translateFieldNames = (message: string): string => {
  let result = message
  for (const [field, translation] of Object.entries(FIELD_TRANSLATIONS)) {
    result = result.replace(new RegExp(`\\b${field}\\b`, 'gi'), translation)
  }
  return result
}

const translateErrorRules = (message: string): string => {
  let result = message
  for (const [rule, translation] of Object.entries(ERROR_RULE_TRANSLATIONS)) {
    result = result.replace(new RegExp(`\\b${rule}\\b`, 'gi'), translation)
  }
  return result
}

export const translateError = (error: string): string => {
  let result = cleanLaravelErrorMessage(error)
  result = result.charAt(0).toUpperCase() + result.slice(1)
  result = translateFieldNames(result)
  result = translateErrorRules(result)
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
