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

const MESSAGE_TRANSLATIONS: Record<string, string> = {
  'Invalid login details': 'Nieprawidłowy email lub hasło',
  'Invalid credentials': 'Nieprawidłowe dane logowania',
  'These credentials do not match our records': 'Podane dane nie pasują do naszych rekordów',
  'The email field is required': 'Pole email jest wymagane',
  'The password field is required': 'Pole hasło jest wymagane',
  'The email field must be a valid email address': 'Pole email musi być poprawnym adresem email',
  Unauthenticated: 'Nieautoryzowany dostęp',
  'Network Error': 'Błąd sieci. Sprawdź połączenie z internetem',
  'Request failed with status code 401': 'Nieprawidłowy email lub hasło',
  'Request failed with status code 422': 'Nieprawidłowe dane formularza',
  'Request failed with status code 500': 'Błąd serwera. Spróbuj ponownie później',
} as const

const cleanLaravelErrorMessage = (error: string): string => {
  return error.replace(/^The\s+/i, '').replace(/\s+field\s+is\s+/, ' ')
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

export const translateMessage = (message: string): string => {
  if (MESSAGE_TRANSLATIONS[message]) {
    return MESSAGE_TRANSLATIONS[message]
  }
  return message
}

export const translateError = (error: string): string => {
  // First check for direct translation
  if (MESSAGE_TRANSLATIONS[error]) {
    return MESSAGE_TRANSLATIONS[error]
  }

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
