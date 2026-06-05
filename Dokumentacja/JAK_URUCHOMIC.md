# Jak odpalic projekt

## Gdzie jest baza danych?

Domyslnie projekt uzywa **SQLite** — plik bazy to:

```
ProjektAplikacje/database/database.sqlite
```

To jeden plik na dysku. Laravel tworzy go automatycznie przy migracjach.

**Wazne:** triggery i funkcja skladowa z wymagan na 5.0 dzialaja tylko na **MySQL/MariaDB**. Na SQLite aplikacja dziala normalnie, ale logika triggerow jest pomijana (salda liczy PHP).

Zeby miec pelne triggery do obrony/prezentacji, ustaw w `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rozliczenia
DB_USERNAME=root
DB_PASSWORD=
```

Potem utworz baze `rozliczenia` w phpMyAdmin/HeidiSQL i odpal `php artisan migrate:fresh --seed`.

---

## Wymagania

- PHP 8.2+
- Composer
- Node.js + npm

---

## Pierwsze uruchomienie

```powershell
cd "sciezka\do\ProjektAplikacje"

# 1. Zaleznosci
php "$env:LOCALAPPDATA\Microsoft\WinGet\Links\composer.phar" install
npm install

# 2. Konfiguracja
copy .env.example .env
php artisan key:generate

# 3. Baza (SQLite — plik powstanie sam)
New-Item database\database.sqlite -ItemType File -Force
php artisan migrate:fresh --seed

# 4. Frontend
npm run build

# 5. Serwer
php artisan serve
```

Aplikacja: **http://127.0.0.1:8000**

---

## Kolejne uruchomienia

```powershell
cd ProjektAplikacje
php artisan serve
```

Jesli edytujesz CSS/JS, w drugim terminalu:

```powershell
npm run dev
```

---

## Konta testowe (po seedzie)

| Rola  | Email               | Haslo    |
|-------|---------------------|----------|
| admin | krystian@example.com | password |
| user  | adam@example.com     | password |
| user  | ewa@example.com      | password |

---

## Co przeklikac zeby sprawdzic (demo na 5 min)

1. Zaloguj sie jako **krystian@example.com** / `password`
2. **Dashboard** → **Zarzadzaj moimi grupami**
3. Otworz grupe **Wycieczka w gory 2026**
4. Sprawdz **Panel rozliczen** — salda czlonkow (Krystian +400, reszta -200)
5. Sprawdz rachunek **Obiad i napoje** — widac podzial `bill_splits` i pozycje **Duza Pizza**
6. Dodaj nowy wydatek — wybierz platnika z listy
7. Rozwin **Dodaj pozycje z paragonu** — dodaj pozycje i przypisz osoby
8. Wejdz w **Admin** (tylko admin) — zmien role uzytkownika, usun konto
9. Wyloguj, zaloguj jako **adam@example.com** — widzi te sama grupe
10. Utworz nowa grupe, dodaj czlonka po e-mailu

---

## Typowe problemy

**`php` nie jest rozpoznawane** — zamknij i otworz terminal albo dodaj PHP do PATH.

**Biala strona / brak styli** — odpal `npm run build`.

**Blad bazy** — sprawdz czy istnieje `database/database.sqlite` albo czy MySQL dziala.

---

Pelna dokumentacja projektu (ERD, SQL, triggery, algorytmy): `../Dokumentacja/DOKUMENTACJA.md` lub PDF `dokumentacja.pdf`
