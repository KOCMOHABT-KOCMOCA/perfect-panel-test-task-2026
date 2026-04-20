# Perfect Panel Backend Test решение

## Задание 1 (SQL)

```sql
SELECT 
    u.id AS ID,
    CONCAT(u.first_name, ' ', u.last_name) AS Name,
    b.author AS Author,
    GROUP_CONCAT(DISTINCT bk.name ORDER BY bk.name SEPARATOR ', ') AS Books
FROM users u
JOIN user_books ub ON ub.user_id = u.id
JOIN books b ON b.id = ub.book_id
JOIN books bk ON bk.id = ub.book_id
WHERE TIMESTAMPDIFF(YEAR, u.birthday, CURRENT_DATE) BETWEEN 7 AND 17
  AND TIMESTAMPDIFF(DAY, ub.get_date, COALESCE(ub.return_date, CURRENT_DATE)) <= 14
GROUP BY u.id, u.first_name, u.last_name, b.author
HAVING COUNT(DISTINCT ub.book_id) = 2 
   AND COUNT(DISTINCT b.author) = 1;
```

Для оптимизации производительности можно добавить индексы:
```sql
CREATE INDEX idx_users_birthday ON users(birthday);
CREATE INDEX idx_user_books_dates ON user_books(get_date, return_date);
```

## Задание 2 — Запуск приложения с API

```bash
docker compose up -d --build
```

Примеры запросов:
```bash

curl -X GET \
  'http://localhost:8080/api/v1?method=rates&currency=BTC,ETH,USD' \
  -H 'Authorization: Bearer a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6A7B8C9D0E1F2G3H4' \
  -H 'accept: application/json'

curl -X POST -H "Authorization: Bearer a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6A7B8C9D0E1F2G3H4" \
     -d "currency_from=BTC&currency_to=USD&value=3" \
     "http://localhost:8080/api/v1?method=convert"
```



