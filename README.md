# LAMPSchool Ver. 2020.6 - Repository di studio e analisi 

## Debito tecnico - compatibilità versioni php


**IL CODICE IN QUESTO REPOSITORY NON E' DA CONSIDERARSI COME CODICE IN SVILUPPO, NON USARE COME CODICE IN PRODUZIONE,
LO SCOPO PRIMARIO DI QUESTO REPOSITORY È L'ANALISI DELLA CODEBASE DI LAMP-SCHOOL AL FINE DI QUANTIFICARNE IL DEBITO TECNICO.**

### Contenuto

**CODEBASE**

- LampSchool versione 2020.1 - sha [7650d360d4685c0024dd7af250830e1aa328416f](https://github.com/scaforchio/LAMPSchool/commit/7650d360d4685c0024dd7af250830e1aa328416f)

**TOOLS**

- [PHP_CODESNIFFER](https://github.com/squizlabs/PHP_CodeSniffer) con plugin [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility)
  
  configurazione minima per check compatibilità.

- [RECTOR](https://github.com/rectorphp/rector)

  configurazione incompleta (dipende dal tipo di refactoring che si vuole effettuare automatico|manuale|misto)

- Todo: installare [ECS](https://github.com/symplify/easy-coding-standard)
  da usare in congiunzione con rector (precedentemente và stabilito e configurare il coding standard da applicare alla codebase)

### Installazione e analisi


```bash
$ git clone https://github.com/RegitSchool/ls-analisi-compabilita-php.git

$ ls ls-analisi-compabilita-php/

$ composer install

$ composer bin all install
```

Verifica compatibilità php5.6

```bash
$ ./vendor/bin/phpcs --standard=phpcs.compatibility-56.xml -v
```

Verifica compatibilità php da 7.0 a 7.4

```bash
$ ./vendor/bin/phpcs --standard=phpcs.compatibility-70-74.xml -v
```
