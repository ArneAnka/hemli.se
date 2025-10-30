# Hemli.se — säker fildelning med klient-sidig kryptering

Hemli.se krypterar filer i webbläsaren innan uppladdning. Servern lagrar endast krypterad data och saknar teknisk möjlighet att dekryptera innehållet (zero-knowledge-modell).

## Funktioner

- Klient-sidig kryptering (WebCrypto, AES-256-GCM)
- Nyckel stannar hos avsändaren (zero-knowledge)
- Engångslänkar och automatisk radering efter nedladdning
- Testläge utan konto (“Prova utan konto”, upp till 5 MB)
- Mörkt/ljust läge, minimalistiskt UI
- Minimal metadata

Planerat:
- Konton och större filgränser
- Betald nivå
- Delade mappar/rättigheter

## Säkerhetsmodell

| Egenskap | Status |
| --- | --- |
| Klient-sidig kryptering | Ja |
| Servern lagrar bara cipher-text | Ja |
| Nycklar lagras på server | Nej |
| IV i query, nyckel i URL-fragment | Ja |
| Engångslänk (raderas efter hämtning) | Ja |

Servern hanterar endast: krypterad blob, filstorlek/MIME, tidsstämplar, engångstoken.

## Gästuppladdning: flöde

1. Användaren väljer fil i webbläsaren.  
2. WebCrypto genererar ny AES-GCM-nyckel och IV.  
3. Filen krypteras lokalt.  
4. Endast cipher-text laddas upp.  
5. Delningslänk skapas: `.../try/v/{token}?iv=...#key=...`  
   - `iv` i query  
   - `key` i fragmentet (`#` skickas inte till servern)  
6. Mottagaren öppnar länken; klienten hämtar cipher (`/try/blob/{token}`), dekrypterar lokalt och startar nedladdning.  
7. Efter första hämtningen markeras länken som använd och filen raderas.

## Teknisk översikt

- Backend: Laravel  
- Frontend: Vue (Inertia)  
- UI: TailwindCSS, shadcn-ui  
- Kryptering: WebCrypto AES-GCM  
- Auth: Laravel Fortify (registrering avstängd i produktion tills konton är klara)