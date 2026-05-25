CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role VARCHAR(20) DEFAULT 'vendedor' CHECK (role IN ('admin', 'vendedor', 'empleado')),
    commission_rate DECIMAL(5,2) DEFAULT 0,
    bonus_per_10_units DECIMAL(10,2) DEFAULT 0,
    employee_status VARCHAR(10) DEFAULT 'activo' CHECK (employee_status IN ('activo', 'inactivo')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    full_name VARCHAR(150) NOT NULL,
    cedula_rif VARCHAR(30) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, cedula_rif)
);

CREATE TABLE IF NOT EXISTS raw_materials (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    stock DECIMAL(12,2) DEFAULT 0,
    unit_cost_usd DECIMAL(12,4) DEFAULT 0,
    min_stock DECIMAL(12,2) DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    type VARCHAR(10) NOT NULL CHECK (type IN ('simple', 'compuesto')),
    stock DECIMAL(12,2) DEFAULT NULL,
    sale_price_usd DECIMAL(12,2) NOT NULL,
    production_cost_usd DECIMAL(12,4) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recipe_items (
    id SERIAL PRIMARY KEY,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    raw_material_id INT NOT NULL REFERENCES raw_materials(id) ON DELETE CASCADE,
    quantity DECIMAL(12,4) NOT NULL,
    UNIQUE (product_id, raw_material_id)
);

CREATE TABLE IF NOT EXISTS sales (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    client_id INT NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    employee_id INT DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
    sale_type VARCHAR(10) NOT NULL CHECK (sale_type IN ('contado', 'credito')),
    total_usd DECIMAL(12,2) NOT NULL,
    total_bs DECIMAL(20,2) NOT NULL,
    exchange_rate DECIMAL(12,4) NOT NULL,
    status VARCHAR(10) DEFAULT 'pendiente' CHECK (status IN ('pagada', 'pendiente', 'cancelada')),
    due_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sale_items (
    id SERIAL PRIMARY KEY,
    sale_id INT NOT NULL REFERENCES sales(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity DECIMAL(12,2) NOT NULL,
    unit_price_usd DECIMAL(12,2) NOT NULL,
    subtotal_usd DECIMAL(12,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS payments (
    id SERIAL PRIMARY KEY,
    sale_id INT NOT NULL REFERENCES sales(id) ON DELETE CASCADE,
    amount_usd DECIMAL(12,2) NOT NULL,
    amount_bs DECIMAL(20,2) NOT NULL,
    exchange_rate DECIMAL(12,4) NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exchange_rates (
    id SERIAL PRIMARY KEY,
    rate DECIMAL(12,4) NOT NULL,
    source VARCHAR(10) DEFAULT 'api' CHECK (source IN ('api', 'manual')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS employee_production (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity INT NOT NULL,
    bonus_earned DECIMAL(10,2) DEFAULT 0,
    produced_at DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS employee_payments (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    payment_type VARCHAR(10) NOT NULL CHECK (payment_type IN ('bono', 'comision', 'salario')),
    amount_usd DECIMAL(12,2) NOT NULL,
    units_produced INT DEFAULT 0,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS expenses (
    id SERIAL PRIMARY KEY,
    type VARCHAR(20) NOT NULL CHECK (type IN ('materia_prima', 'empleado', 'otro')),
    description VARCHAR(255) NOT NULL,
    amount_usd DECIMAL(12,2) NOT NULL,
    expense_date DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS finance_periods (
    id SERIAL PRIMARY KEY,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    total_sales_usd DECIMAL(12,2) DEFAULT 0,
    total_expenses_usd DECIMAL(12,2) DEFAULT 0,
    gross_profit_usd DECIMAL(12,2) DEFAULT 0,
    commission_10pct_usd DECIMAL(12,2) DEFAULT 0,
    net_profit_usd DECIMAL(12,2) DEFAULT 0,
    savings_usd DECIMAL(12,2) DEFAULT 0,
    dividends_usd DECIMAL(12,2) DEFAULT 0,
    other_allocations_usd DECIMAL(12,2) DEFAULT 0,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (period_start, period_end)
);
