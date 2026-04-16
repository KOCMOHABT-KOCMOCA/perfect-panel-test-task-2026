#!/bin/bash

if [[ $# -eq 0 ]]
then
    domain=localhost:8080
else
    domain=$1
fi

token=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6A7B8C9D0E1F2G3H4

ssl=0
if [[ $ssl -eq 1 ]]
then
    scheme=https
else
    scheme=http
fi

## 1. Общие тесты авторизации
# 1.1 Нет заголовка Authorization → ошибка 403
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" -i

# 1.2 Неправильный токен
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer WRONGTOKEN123" -i

# 1.3 Правильный токен (базовый успешный запрос — используем дальше)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer {$token}" -i

# 1.4 Токен без "Bearer " (только значение)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: {$token}" -i

# 1.5 Токен в другом регистре (проверка case-sensitivity)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer {$token}" -i

# 1.6 Лишний пробел в токене
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer  {$token} " -i

## 2. Тесты метода rates
#2.1 Позитивные сценарии
# 2.1.1 Все курсы (без параметра currency) — проверка сортировки от меньшего к большему
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer {$token}"

# 2.1.2 Одна валюта
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=USD" \
  -H "Authorization: Bearer {$token}"

# 2.1.3 Несколько валют (через запятую, как указано в ТЗ)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=USD,EUR,RUB,BTC" \
  -H "Authorization: Bearer {$token}"

# 2.1.4 Валюты в другом порядке и с пробелами (проверка trimming)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=BTC, RUB , EUR" \
  -H "Authorization: Bearer {$token}"


## 2.2 Негативные и пограничные сценарии
# 2.2.1 Несуществующая валюта
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=XXX" \
  -H "Authorization: Bearer {$token}"

# 2.2.2 Пустое значение параметра currency
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=" \
  -H "Authorization: Bearer {$token}"

# 2.2.3 Дубли валют
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=USD,USD,USD" \
  -H "Authorization: Bearer {$token}"

# 2.2.4 Лишние неизвестные параметры (проверка игнорирования)
curl -X GET "{$scheme}://{$domain}/api/v1?method=rates&currency=USD&foo=bar&debug=true" \
  -H "Authorization: Bearer {$token}"

# 2.2.5 Отсутствует параметр method
curl -X GET "{$scheme}://{$domain}/api/v1" \
  -H "Authorization: Bearer {$token}"

# 2.2.6 Неправильное название метода
curl -X GET "{$scheme}://{$domain}/api/v1?method=ratez" \
  -H "Authorization: Bearer {$token}"

2.3 Тесты HTTP-метода
# 2.3.1 POST вместо GET на rates (должен отдавать ошибку)
curl -X POST "{$scheme}://{$domain}/api/v1?method=rates" \
  -H "Authorization: Bearer {$token}"


## 3. Тесты метода convert
# 3.1 Позитивные сценарии
# 3.1.1 USD → BTC
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=1.00" \
  -H "Authorization: Bearer {$token}"

# 3.1.2 BTC → USD (обратная конвертация)
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=BTC&currency_to=USD&value=1.00" \
  -H "Authorization: Bearer {$token}"

# 3.1.3 Конвертация в ту же валюту (USD → USD)
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=USD&value=100.50" \
  -H "Authorization: Bearer {$token}"


## 3.2 Граничные значения value
# 3.2.1 Zero value
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=0" \
  -H "Authorization: Bearer {$token}"

# 3.2.2 Negative value
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=-50.25" \
  -H "Authorization: Bearer {$token}"

# 3.2.3 Очень маленькое значение
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=0.0000000001" \
  -H "Authorization: Bearer {$token}"

# 3.2.4 Очень большое значение
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=9999999999.99" \
  -H "Authorization: Bearer {$token}"

# 3.2.5 value без десятичной части
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=1" \
  -H "Authorization: Bearer {$token}"

3.3 Негативные сценарии ввода
# 3.3.1 value — не число (строка)
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=abc" \
  -H "Authorization: Bearer {$token}"

# 3.3.2 Несуществующие валюты
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=XXX&currency_to=YYY&value=1" \
  -H "Authorization: Bearer {$token}"

# 3.3.3 Отсутствует currency_from
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_to=BTC&value=1.00" \
  -H "Authorization: Bearer {$token}"

# 3.3.4 Отсутствует currency_to
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&value=1.00" \
  -H "Authorization: Bearer {$token}"

# 3.3.5 Отсутствует value
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC" \
  -H "Authorization: Bearer {$token}"

# 3.3.6 Дубли параметров (один и тот же параметр дважды)
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_from=EUR&currency_to=BTC&value=1" \
  -H "Authorization: Bearer {$token}"

# 3.3.7 Лишние параметры
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=1&commission=5&note=test" \
  -H "Authorization: Bearer {$token}"

3.4 Тесты HTTP-метода и формата
# 3.4.1 GET вместо POST на convert (должен отдавать ошибку)
curl -X GET "{$scheme}://{$domain}/api/v1?method=convert&currency_from=USD&currency_to=BTC&value=1" \
  -H "Authorization: Bearer {$token}"

# 3.4.2 Параметры в body (application/x-www-form-urlencoded) — проверка, что API строго следует query-string
curl -X POST "{$scheme}://{$domain}/api/v1?method=convert" \
  -H "Authorization: Bearer {$token}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "currency_from=USD&currency_to=BTC&value=1.00"


