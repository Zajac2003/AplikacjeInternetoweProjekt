# Jak odpalic projekt

## Gdzie jest baza danych?

Domyslnie projekt uzywa **SQLite** — plik bazy:

```
database/database.sqlite
```

Laravel tworzy go automatycznie przy migracjach.

**Wazne:** triggery z wymagan na 5.0 dzialaja tylko na **MySQL**. Na SQLite aplikacja chodzi normalnie, salda liczy PHP.

---

## Wymagania

- PHP 8.2+
- Composer
- Node.js + npm

---

## Pierwsze uruchomienie

```powershell
cd "sciezka\do\ProjektAplikacje"

composer install
npm install

copy .env.example .env
php artisan key:generate

New-Item database\database.sqlite -ItemType File -Force
php artisan migrate:fresh --seed

npm run build
php artisan serve
```

Aplikacja: **http://127.0.0.1:8000**

---

## Kolejne uruchomienia

```powershell
php artisan serve
```

CSS/JS w drugim terminalu:

```powershell
npm run dev
```

---

## Konta testowe (po seedzie)

| Rola  | Email                | Haslo    |
|-------|----------------------|----------|
| admin | krystian@example.com | password |
| user  | adam@example.com      | password |
| user  | ewa@example.com       | password |

---

## Szybki test (5 min)

1. Zaloguj: **krystian@example.com** / `password`
2. **Moje Grupy** → **Wycieczka w gory 2026**
3. Sprawdz panel rozliczen i historie rachunkow
4. Dodaj wydatek, pozycje z paragonu
5. Wejdz w **Admin** (tylko admin)

---

## Problemy

- **`php` nie dziala** — zamknij terminal i otworz nowy
- **Brak styli** — `npm run build`
- **Blad bazy** — sprawdz `database/database.sqlite`

---

Dokumentacja (PDF, ERD, SQL): `../Dokumentacja/`
