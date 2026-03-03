# Railway DB Variables – Backend servisine ekle

**Hata:** `mysql:host=loca...` = Backend localhost'a bağlanıyor → DB değişkenleri Backend servisine EKLENMEMİŞ.

`DB_*` değişkenleri **MySQL servisinde değil**, **Backend (PHP) servisinde** olmalı.

## Adımlar

1. **Backend** servisini aç (PHP/Dockerfile kullanan servis).
2. **Variables** sekmesine git.
3. **Raw Editor** veya **+ New Variable** ile şunları ekle:

| Variable | Nereden alınacak |
|----------|------------------|
| `DB_HOST` | MySQL servisi → Variables → `MYSQLHOST` değerini kopyala veya **Reference** ile bağla |
| `DB_NAME` | MySQL → `MYSQLDATABASE` |
| `DB_USER` | MySQL → `MYSQLUSER` |
| `DB_PASS` | MySQL → `MYSQLPASSWORD` |

**Reference ile:** Backend → Variables → New Variable → **Add Reference** → MySQL servisini seç → MYSQLHOST’u seç → Variable adını `DB_HOST` yap. Aynı şekilde diğerlerini ekle.

4. Backend için **Redeploy** çalıştır.
