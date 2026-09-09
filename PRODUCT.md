# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Central Store Administrators & Operations Managers:** Managing store inventory, catalog products, customer credit/accounts, supplier purchase orders, bills payable, and financial transactions from the central web dashboard.
- **External Field Sales Representatives:** Generating sales orders on the road via the companion mobile app with offline-first synchronization, synced back to the central platform.

## Product Purpose

Garagem provides an end-to-end commercial operations and enterprise management platform for wholesale and distribution businesses. It unifies inventory control, customer accounts, multi-channel sales execution (in-store and field reps), accounts payable, cash-flow balances, and marketing banner generation into a centralized, auditable system.

## Positioning

A unified, real-time commercial distribution hub that bridges desktop back-office administration with resilient, offline-first mobile sales execution—ensuring consistent stock tracking, customer credit monitoring, and financial reconciliation without operational downtime.

## Operating Context

- **Back-Office Environment:** Desktop browser-based workflow used daily for inventory adjustments, order verification, customer profile reviews, bills payable management, and financial reporting.
- **Field Sales Integration:** Mobile agents recording sales in offline/intermittent connectivity conditions, pushing batched transactions that update central stock and receivables.
- **Financial & Auditing Requirements:** Cash balance accounting in integer cents (BRL / R$), pending vs. confirmed receivables distinction, and strict audit logging for transactions, purchases, and customer changes.

## Capabilities and Constraints

- **Multi-Role Administration:** Granular permission system (Spatie Permission) protecting dashboard, sales, products, banners, reports, and administrative settings.
- **Commercial Operations:** Catalog management with brands, categories, unit weights, and stock counts; point-of-sale order creation; customer account balance tracking.
- **Financial Engine:** Transaction tracking (`bills_payable`, `financial_transactions`, `account_balances`) with integer cent precision and daily/monthly cash balance computation.
- **Banner Studio:** Built-in studio for generating promotional product banners and marketing graphics using Browsershot/Puppeteer.
- **Offline Sync Gateway:** RESTful API with Sanctum OTP authentication serving dashboard metrics and batched offline sales pushes from mobile devices.
- **Language & Locale:** Brazilian Portuguese (pt-BR) business conventions, phone formatting, CPF/CNPJ identifiers, and BRL currency formatting.

## Brand Commitments

- **Name:** Garagem
- **Tone:** Professional, reliable, pragmatic, and clear. Focused on operational efficiency and financial accuracy.

## Evidence on Hand

- Fully operational Laravel 13, Livewire 4, and Tailwind CSS 4 codebase.
- Defined database schema, seeders, and permission matrices.
- Functional Livewire components for Dashboard, Sales, Products, Customers, Orders, Bills Payable, Banners, and Reports.
- Mobile API documentation (`docs/dashboard-api-mobile-agent.md`, `docs/sales-push-api-mobile-agent.md`).

## Product Principles

- **Financial & Stock Integrity First:** Never sacrifice accuracy or auditability in stock levels, ledger transactions, or receivable balances.
- **Uninterrupted Commercial Flow:** Enable back-office operators and mobile field reps to execute sales smoothly without interface friction or blocking synchronization locks.
- **Scannable & High-Density UI:** Provide operations managers with immediate visibility into mission-critical metrics (cash balance, daily sales, pending bills) without visual clutter.
- **Clarity in State & Feedback:** Ensure every financial status (pending, paid, cancelled) and transaction state is unmistakably evident.
