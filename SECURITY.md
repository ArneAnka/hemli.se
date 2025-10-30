### `SECURITY.md`

# Security Policy

Hemli.se prioriterar konfidentialitet och klient-sidig säkerhet. Den här policyn beskriver hur sårbarheter ska rapporteras och vad som ingår i säkerhetsområdet.

## Rapportera sårbarheter

- Använd GitHub Security Advisories eller skicka ett privat mejl till security@hemli.se.
- Inkludera en tydlig beskrivning, reproduktionssteg, påverkad yta och ev. PoC.
- Skicka inte känslig data i klartext. Använd helst en privat kanal. (PGP-nyckel kan tillhandahållas på begäran.)
- Skapa inte publika issues för säkerhetsärenden.

Vi bekräftar mottagande inom 3 arbetsdagar och ger en initial bedömning inom 10 arbetsdagar.

## Omfattning

I scope:
- Hemli.se backend (Laravel) och offentliga endpoints
- Klientlogik för kryptering/dekryptering (WebCrypto)
- Engångslänkar, token-hantering, utgångslogik
- Lagring och rensning av krypterade blobbar

Utanför scope:
- Tredjepartsberoenden utanför vår kontroll
- Social engineering
- DoS/DDoS och kapacitetsattacker
- Sårbarheter som kräver root/admin på klientens enhet eller webbläsares utvecklarverktyg

## Testregler

- Testa endast med egna konton/filer.  
- Ingen exfiltration av andra användares data.  
- Ingen avsiktlig service-degradering eller volymtester som påverkar driften.  
- Följ gällande lagstiftning.

Vid osäkerhet, kontakta oss innan test.

## Kända säkerhetsprinciper i produkten

- All filkryptering sker i webbläsaren (AES-256-GCM via WebCrypto).
- Endast cipher-text och minimal metadata lagras på servern.
- Delningslänk: IV i query; nyckel i URL-fragment (fragmentet skickas inte till servern).
- Engångslänkar: efter första hämtning markeras posten som använd (`used_at`) och filen raderas.
- Registrering är avstängd i produktion tills kontofunktioner är klara.

## Ansvarsfull offentliggöring

Publicera inte detaljer om sårbarheter förrän vi har haft skälig tid att åtgärda dem och rulla ut fixar. Om du vill bli krediterad i release-notiser, meddela oss i din rapport.

## Kontakt

security@hemli.se
