# ZenLedger Website

A college project for **SWE4713** — an accounting/budgeting program built iteratively across three agile sprints.

---

## Overview

This project is a PHP-based web application that replicates the core pages and functionality of the ZenLedger website. It was developed as part of a software engineering course, with work divided into three sprints to simulate an agile development workflow.

**Tech Stack:**
- **PHP** — server-side templating and page logic (92%)
- **CSS** — styling and layout (5%)
- **JavaScript** — interactivity and client-side behavior (2%)

**Project Structure:**
```
ZenLedgerWebsite_Public/
├── FirstSprint/          # Sprint 1 deliverables
├── SecondSprint/         # Sprint 2 deliverables
├── ThirdSprint/          # Sprint 3 deliverables
└── ZenLedgerWebsite/     # Final integrated website
```

Each sprint folder contains the work completed during that iteration, and `ZenLedgerWebsite/` holds the final, consolidated version of the site.

---

## Setup

### Prerequisites

- **PHP 7.4+** — [Download PHP](https://www.php.net/downloads)
- A local web server such as [XAMPP](https://www.apachefriends.org/), [MAMP](https://www.mamp.info/), or PHP's built-in server

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ebarker21/ZenLedgerWebsite_Public.git
   cd ZenLedgerWebsite_Public
   ```

2. **Navigate to the final site directory:**
   ```bash
   cd ZenLedgerWebsite
   ```

3. **Start a local PHP server:**
   ```bash
   php -S localhost:8000
   ```

4. **Open your browser and visit:**
   ```
   http://localhost:8000
   ```

> Alternatively, place the project folder in your XAMPP/MAMP `htdocs` directory and access it through `http://localhost/ZenLedgerWebsite`.

---

## Usage

Once the server is running, you can browse the site locally just like you would the live ZenLedger website. Navigate through the available pages to explore the recreated UI and features.

To review the work from a specific sprint, start the PHP server from within that sprint's folder instead:

```bash
cd FirstSprint
php -S localhost:8000
```

This is useful for comparing how the site evolved across each development iteration.
