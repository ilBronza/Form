# IlBronza Form

Pacchetto Laravel per costruire form strutturati in **fieldset**, con markup **UIkit 3**, integrazione con **Eloquent** e flussi tipici CRUD (create, edit, show, PDF). I singoli campi sono istanze di `IlBronza\FormField\FormField`, create tramite il pacchetto companion **ilbronza/formfield** (`FormFieldsProvider`).

[![Latest Version on Packagist][ico-version]][link-packagist]

---

## Requisiti e dipendenze

- **Laravel** (il service provider è auto-scoperto da `composer.json`).
- **`IlBronza\FormField`** — definizione tipi di campo, validazione lato definizione, rendering del controllo. Questo repository (`ilbronza/form`) orchestr **fieldset** e **Form**; i **FormField** vivono nel pacchetto FormField.
- In uso nel codice compaiono anche: **`IlBronza\Buttons`**, trait **`IlBronza\CRUD`** (classi HTML / navbar), **`IlBronza\UikitTemplate\Fetcher`** (extra views). Per una pagina completa come `form::uikit.form` serve un layout coerente (es. `uikittemplate::app`).

Se manca una di queste dipendenze, installarle nel progetto host o adattare le view.

---

## Installazione

```bash
composer require ilbronza/form
```

Pubblicare la configurazione (opzionale):

```bash
php artisan vendor:publish --tag=form.config
```

Namespace view e traduzioni: **`form::`** (es. `form::uikit.form`, chiavi `form::form.*`).

---

## Concetti: `Form`, `FormFieldset`, `FormField`

| Componente | Ruolo |
|------------|--------|
| **`IlBronza\Form\Form`** | Contenitore del form: action, method, modello, fieldset o lista piatta di campi, titolo, card, pulsanti di chiusura, modalità di visualizzazione (`form` / `show` / `pdf`). |
| **`IlBronza\Form\FormFieldset`** | Raggruppa campi (e altri fieldset annidati), legenda, griglia UIkit, larghezza colonna, collapse, ruoli di visibilità a livello fieldset. |
| **`IlBronza\FormField\FormField`** | Singolo input (text, select, relazioni, ecc.), creato da **`FormFieldsProvider::createByNameParameters($nomeCampo, $parametri)`**. |

Il flusso tipico è: definisci **parametri** (array o `FieldsetParametersFile`) → **`FieldsetsProvider`** li trasforma in `FormFieldset` + `FormField` → li agganci a un **`Form`** → **`$form->render()`** (o `_render()` per il partial).

---

## Classe `Form`: uso programmatico

### Istanza

Il container registra un singleton `form`:

```php
$form = app('form'); // oppure alias Form se configurato nel progetto
```

Oppure `new \IlBronza\Form\Form()`.

### Modello, action, method

```php
$form->setModel($user);
$form->setAction(route('users.update', $user));
$form->setMethod('PUT'); // la view aggiunge @method se non è GET/POST
```

### Campi senza fieldset

```php
$form->addFormField($formField); // $formField è FormField già costruito
```

### Fieldset

```php
$fieldset = $form->addFormFieldset('anagrafica', [
    'width' => 1,
    'columns' => 2,
    'showLegend' => true,
]);
$fieldset->addFormField($campoNome);
```

Oppure aggiungere un `FormFieldset` già creato:

```php
$form->addFieldset($fieldset);
```

Per appiattire tutti i campi dei fieldset in un’unica lista (e svuotare i fieldset):

```php
$form->flattenFieldsets();
```

### Ricerca campo per nome

```php
$field = $form->getFieldByName('email'); // false se assente
```

### Rendering

| Metodo | Uso |
|--------|-----|
| **`render()`** | View `form::uikit.form` — estende `uikittemplate::app` e include il contenuto del form. |
| **`_render()`** | Partial `form::uikit._form` — card, contenuto, footer pulsanti; adatto a embed in layout custom. |
| **`_renderAjax()`** | `form::uikit._ajax`. |
| **`_renderShow()`** / **`_renderPdf()`** | Impostano `displayMode` su `show` o `pdf` e delegano a `_render()`. |
| **`renderContent()`** | Solo il blocco contenuto (`_content`) come stringa HTML. |

La modalità **`displayMode`** controlla come ogni `FormField` viene disegnato in `_fields.blade.php`: `render()`, `renderShow()` o `renderPdf()`.

### Aspetto e UX

- **`setHorizontalForm()`** / **`setVerticalForm()`** / **`setStackedForm()`** — classi UIkit (`uk-form-horizontal` vs stacked).
- **`setTitle()`**, **`setIntro()`**, **`setShowTitle()`**, **`setHideTitle()`**.
- **`hasCard()`**, **`setCard()`**, **`addCardClasses()`** — card UIkit; classi create/edit possono essere influenzate da `config('form.createCardClasses')` / `editCardClasses` nel progetto che le applica.
- **`setCollapsible()`**, **`setCollapsedInitially()`** — senza card aggiungono il toggle prima del tag `<form>` e collassano l'intero form; con card lo aggiungono nell'header, prima del titolo, e collassano solo il primo `uk-card-body`.
- **`setCancelHref()`**, pulsanti submit / “save and …” tramite **`FormButtonsTrait`** (`setHasSubmitButton`, `addSaveAndNewButton`, `addClosureButton`, ecc.).
- **`addExtraView($position, $view, $params)`**, **`addFetcher($position, $fetcher)`** — posizioni valide: `outherTop`, `outherBottom`, `innerTop`, `innerBottom`, `left`, `right`, `outherLeft`, `outherRight` (nomi storici con typo “outher”).

### Altri flag utili

- **`setDisplayMode('form'|'show'|'pdf')`**
- **`setAllDatabaseFields()`** / **`getDatabaseField()`** — metadati opzionali per i campi.
- **`hasUpdateEditor()`** — se il modello esiste e `config('form.updateEditor')` è true (salvo override con `setUpdateEditor()`), influisce su **`hasClosureButtons()`** (footer azioni).

Il collapse globale può essere configurato anche in fase di creazione:

```php
$form = Form::createFromArray([
    'collapsible' => true,
    'collapsedInitially' => true,
]);
```

Quando il form viene costruito tramite `CrudModelFormHelper`, le stesse chiavi possono essere configurate ad alto livello nel `FieldsetParametersFile` o nel controller. Il controller prevale sul parameters file.

```php
// Nel FieldsetParametersFile
public array $formParameters = [
    'collapsible' => true,
    'collapsedInitially' => true,
];
```

```php
// Nel controller CRUD
public array $formParameters = [
    'collapsible' => true,
    'collapsedInitially' => false,
];
```

La preferenza scelta dall'utente viene mantenuta nella `sessionStorage` del browser. La chiave è generata automaticamente da route, classe del modello e id (oppure `new` in create). Solo in caso di due form equivalenti nella stessa pagina è utile l'override nel controller:

```php
public ?string $collapseStateKey = 'users-edit-secondary-form';
```

---

## Classe `FormFieldset`

### Creazione

```php
use IlBronza\Form\FormFieldset;

$fs = new FormFieldset('contatti', $form, [
    'legend' => 'contatti',       // chiave traduzione se translateLegend è true
    'width' => 1,                 // intero (frazione UIkit) o array es. ["1-2@m", "1-3@l"]
    'columns' => 2,             // intero o array di breakpoint per uk-child-width-*
    'classes' => ['uk-margin-bottom'],
    'containerClasses' => [],
    'showLegend' => true,
    'divider' => true,
    'translationPrefix' => 'fieldsets', // prefisso per __() sulla legenda
    'collapsedInitially' => null,        // null = usa config('form.fieldset_collapsed_by_default')
    'canBeHidden' => true,               // mostra toggle accordion sulla legenda
]);
```

### Modello

- **`setModel()`** / **`setModelRecursively()`** — propaga il modello ai campi e ai fieldset figli.

### Annidamento

- **`addFieldset($altroFormFieldset)`** — fieldset figlio.
- **`addFormFieldset($nome, $parameters)`** — crea un figlio con la stessa API.

### Custom view nel fieldset

Se imposti `$fieldset->view` come array `['name' => 'mia.view', 'variables' => ['var' => 'metodoModello'], 'parameters' => []]`, il template può chiamare metodi sul modello per popolare variabili (**`renderView()`**).

### Visibilità e sicurezza

- **`setVisibility(bool)`** — se false, il partial `_fieldset` non renderizza il blocco (`isVisible()`).
- A livello **array** (provider), il fieldset può avere chiave **`roles`**: visibile solo se l’utente ha **almeno uno** di quei ruoli (`hasAnyRole`); assenza utente → 403.

### Griglia e collapse

- Larghezza contenitore: **`width`** (vedi `getContainerHtmlClasses()`).
- Colonne campi: **`columns`** → `getColumnsClass()`.
- **`hasCollapse()`**, **`hasCollapseRow()`**, **`hasCollapseColumn()`** — se non impostati sul fieldset, leggono **`config('form.collapse')`**, `collapseRow`, `collapseColumn`.
- **`collapsedInitially`**: se il fieldset è collassabile e ha legenda, determina se il corpo parte nascosto; default globale **`fieldset_collapsed_by_default`** in `config/form.php`.

---

## Definizione struttura da array: `FieldsetParametersFile` e `FieldsetsProvider`

### Struttura ad albero

Ogni **chiave di primo livello** è il nome del fieldset. Il valore è un array che può contenere:

- **`fields`** — mappa `nomeCampo => parametri` (parametri passati a `FormFieldsProvider` del pacchetto FormField).
- **`fieldsets`** — fieldset annidati (stessa struttura ricorsiva).
- Opzioni di presentazione: `width`, `columns`, `classes`, `containerClasses`, `showLegend`, `divider`, `roles`, `buttons`, ecc. (tutto ciò che `FormFieldset::manageParameters()` assegna alle proprietà).

Se l’array **non** ha la chiave `fields`, viene interpretato come **solo lista di campi** e viene avvolto automaticamente in un fieldset `default` (`getSanitizedContainerParameters`).

### Shorthand per singolo campo

Ogni campo può essere definito in due modi (vedi `FieldsetParametersFile::parseFieldsetsParameters`):

1. **Esplicito**: `'nome' => ['type' => 'text', 'rules' => 'required|string|max:255', ...]`
2. **Compatto** (una sola chiave): `'nome' => ['text' => 'required|string|max:255']` — la chiave è il **tipo**, il valore è la stringa **rules** (o array dopo sanitizzazione).

È obbligatorio avere **`rules`** dopo il parsing (direttamente o via shorthand). Per tipi speciali (es. **`json`**) la sanitizzazione tratta anche i sotto-campi.

### Parsing centralizzato

```php
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

$file = FieldsetParametersFile::makeByParameters($arrayFieldsets);
$parametri = $file->getFieldsetsParameters();
```

`setParameters()` esegue: sanificazione container → `parseFieldsParameters()` su tutto l’albero.

### Inserire campi in una posizione

```php
FieldsetParametersFile::insertFieldsInPosition(
    ['nuovo_campo' => ['type' => 'text', 'rules' => 'nullable']],
    $fieldset['fields'],
    3
);
```

Utile per estendere definizioni statiche da provider o trait.

---

## `FieldsetsProvider`: da parametri a `Form` / modello

Classe base: **`IlBronza\Form\Helpers\FieldsetsProvider\FieldsetsProvider`**.

### Factory

```php
// Da array già pronto (es. da FieldsetParametersFile)
$provider = FieldsetsProvider::setFieldsetsParametersByArray($parameters, $model);

// Da classe che estende FieldsetParametersFile e implementa _getFieldsetsParameters()
$provider = FieldsetsProvider::setFieldsetsParametersByFile($file, $model);
```

### Collegare al form

```php
FieldsetsProvider::addFieldsetsToFormByParametersFile($form, $file, $model);
// oppure
$provider->setForm($form);
$provider->setParametersByFile($file);
$provider->setFieldsetsCollectionToForm();
```

### Solo collezione di fieldset

```php
$collection = $provider->getFieldsetsCollection();
// oppure con modello assegnato ricorsivamente:
$provider->setFieldsetsCollectionToModel();
```

Per ogni fieldset, **`filterByRolesAndPermissions`** rimuove dall’array i campi che l’utente non può vedere:

- **`roles`** sul campo: l’utente deve avere quel ruolo (`hasRole`) — i superadmin vedono tutto.
- **`permissions`**: richiede `hasAnyPermission` sul campo.

I fieldset con **`roles`** usano **`userCanSeeFieldsetByRoles`** (vedi sopra).

---

## Ruoli e permessi (riepilogo)

| Livello | Chiave | Comportamento |
|---------|--------|-----------------|
| Fieldset | `roles` | Array di ruoli: visibile se `Auth::user()->hasAnyRole(...)`; senza utente → 403. |
| Campo | `roles` | Singolo ruolo o convenzione del trait: filtro con `hasRole`. |
| Campo | `permissions` | `hasAnyPermission`; superadmin escluso dal filtro. |

---

## Configurazione `config/form.php`

| Chiave | Effetto |
|--------|---------|
| `grid-size` | Classe aggiuntiva griglia sul form (default `uk-grid-small`). |
| `divider` | Default divisori griglia (anche fieldset nuovi da form con `setDivider`). |
| `hasCard` | Default uso card (il form espone `hasCard()` / `setCard()`). |
| `updateEditor` | Comportamento editor in update e pulsanti di chiusura. |
| `showIntro` | Se `getIntro()` può mostrare l’intro. |
| `createCardClasses` / `editCardClasses` | Classi extra card (consumate dal progetto che le applica). |
| `collapse`, `collapseRow`, `collapseColumn` | Default collapse griglia sui fieldset. |
| `fieldset_collapsed_by_default` | Accordion: corpo fieldset nascosto all’avvio se `collapsedInitially` è null. |
| `showSelectPlaceholderText` | Usato dal layer FormField/select nel pacchetto host. |

---

## Esempio end-to-end minimale

```php
use IlBronza\Form\Form;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetsProvider;

$parameters = [
    'dati' => [
        'fields' => [
            'name' => ['type' => 'text', 'rules' => 'required|string|max:120'],
            'email' => ['email' => 'nullable|email'],
        ],
        'columns' => 1,
    ],
];

$file = FieldsetParametersFile::makeByParameters($parameters);

$form = new Form();
$form->setModel($user);
$form->setAction(route('users.update', $user));
$form->setMethod('PUT');
$form->setTitle(__('Modifica utente'));

FieldsetsProvider::addFieldsetsToFormByParametersFile($form, $file, $user);

return $form->_render(); // in una view Blade: {!! $form->_render() !!}
```

*(I tipi `text` / `email` e le chiavi esatte dei parametri dipendono da **ilbronza/formfield**.)*

---

## View principali

- **`form::uikit.form`** — pagina intera (estende layout UIkit template).
- **`form::uikit._form`** — form con card, extra views, `_content`, footer.
- **`form::uikit._opening`** — tag `<form>`, CSRF, `@method`.
- **`form::uikit._fieldsets`** / **`_fieldset`** / **`_fields`** — struttura fieldset e loop sui `FormField`.

Per integrare in un layout proprio si usa in genere **`_render()`** o **`renderContent()`**.

---

## Helper aggiuntivo

**`IlBronza\Form\Helpers\FieldsetsExtractorHelper::getFieldsParametersByFieldsetsParametersArray($fieldsets)`** — appiattisce tutti i `fields` di un array fieldsets (anche annidati) in un unico array `nome => parametri` (utile per validazione o export).

Il **`FieldsetsProvider`** espone anche metodi per relazioni Eloquent (`getBindableAttributeFields`, `getExtraTableRelationshipsFields`, ecc.) quando i parametri campo definiscono **`relation`**.

---

## Provider tipizzati per CRUD

Nel pacchetto sono presenti classi come **`CreateFieldsetsProvider`**, **`EditFieldsetsProvider`**, **`ShowFieldsetsProvider`**, **`ValidateStoreFieldsetsProvider`**, ecc., che estendono la base con **`$type`** statico: servono a convenzioni del progetto (es. file parametri diversi per create vs edit). La logica di costruzione fieldset è nella classe base **`FieldsetsProvider`**.

---

## Change log e licenza

- [changelog.md](changelog.md)
- [license.md](license.md)
- [contributing.md](contributing.md)

---

[ico-version]: https://img.shields.io/packagist/v/ilbronza/form.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/ilbronza/form
