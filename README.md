Rafael Males
Estudante de Engenharia Informática
Projeto final de curso – Instituto Politécnico de Gestão e Tecnologia / ISLA 

# MediGest+

MediGest+ é uma aplicação de gestão médica desenvolvida como projeto final de curso. O objetivo é facilitar a gestão de consultas, pacientes e dados clínicos de forma intuitiva e segura.

## 🚀 Funcionalidades

- Autenticação manual de utilizadores (sem Jetstream ou Laravel Breeze)
- Gestão de pacientes, médicos e consultas
- Interface moderna com Livewire (usado apenas onde necessário)
- Organização de código por módulos (Auth, Admin, etc.)

## 🛠️ Tecnologias

- PHP 8.x
- Laravel 12
- MySQL
- Livewire
- Bootstrap (ou Tailwind, dependendo)
- XAMPP (ambiente local)


## 🔐 Autenticação

A autenticação é feita manualmente (sem Breeze, Jetstream ou Volt), com:
- Registo
- Login
- Logout
- Proteção de rotas
- Diferenciação por tipo de utilizador (ex: admin, paciente, médico)


## 📁 Organização do código

- app/Http/Controllers/Auth/ → Lógica de login e registo
- app/Http/Controllers/Admin/ → Painel administrativo
- app/Http/Controllers/Patient/ → Funcionalidades para pacientes
- resources/views/ → Views organizadas por tipo de utilizador
- routes/web.php → Todas as rotas web da aplicação

## 🧪 Testes

- php artisan test


## 📦 Instalação

```bash
git clone https://github.com/Males24/medigest-clean.git
cd medigest-clean
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

