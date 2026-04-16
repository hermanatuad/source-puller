#!/usr/bin/env python3

try:
    import psycopg2
    POSTGRES_AVAILABLE = True
except Exception:
    POSTGRES_AVAILABLE = False


DB_CONFIG = {
    "host": "34.31.172.119",
    "user": "appuser",
    "password": "AppPass!123",
    "port": 5432
}


DATABASES_POSTGRES = {
    "hospital_information_system": """
        CREATE TABLE IF NOT EXISTS patients (
            patient_id VARCHAR(36) PRIMARY KEY,
            national_id VARCHAR(50) UNIQUE,
            medical_record_number VARCHAR(50) UNIQUE NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            date_of_birth DATE,
            gender VARCHAR(10),
            religion VARCHAR(10),
            marital_status VARCHAR(20),
            city VARCHAR(50),
            province VARCHAR(50),
            residential VARCHAR(100),
            race VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS visits (
            visit_id VARCHAR(36) PRIMARY KEY,
            patient_id VARCHAR(36) NOT NULL,
            visit_date TIMESTAMP,
            exit_date TIMESTAMP,
            visit_type VARCHAR(30),
            attending_doctor VARCHAR(100),
            status VARCHAR(30),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        );

        CREATE TABLE IF NOT EXISTS services (
            service_id VARCHAR(36) PRIMARY KEY,
            service_code VARCHAR(30) UNIQUE NOT NULL,
            service_name VARCHAR(100) NOT NULL,
            service_type VARCHAR(30),
            unit_price DECIMAL(12,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS billing (
            billing_id VARCHAR(36) PRIMARY KEY,
            visit_id VARCHAR(36) NOT NULL,
            service_id VARCHAR(36) NOT NULL,
            quantity INT,
            total_amount DECIMAL(12,2),
            billing_date TIMESTAMP,
            FOREIGN KEY (visit_id) REFERENCES visits(visit_id),
            FOREIGN KEY (service_id) REFERENCES services(service_id)
        );
    """
}


def get_postgres_connection(config, database=None):
    if not POSTGRES_AVAILABLE:
        raise RuntimeError("psycopg2 is not available. Install psycopg2-binary first.")

    host = config["host"]
    user = config["user"]
    password = config["password"]
    port = config.get("port", 5432)
    db_name = database or config.get("default_database", "postgres")

    conn = psycopg2.connect(
        host=host,
        user=user,
        password=password,
        port=port,
        dbname=db_name,
    )
    conn.autocommit = True
    return conn


def generate_databases(engine="postgres", postgres_config=None):
    if engine != "postgres":
        raise NotImplementedError("Only PostgreSQL is supported.")

    cfg = postgres_config or DB_CONFIG

    conn = get_postgres_connection(cfg)
    cursor = conn.cursor()

    for db_name, schema in DATABASES_POSTGRES.items():
        try:
            cursor.execute(f'CREATE DATABASE "{db_name}"')
            print(f"Database created: {db_name}")
        except Exception as e:
            if "already exists" in str(e).lower():
                print(f"Database ready: {db_name}")
            else:
                print(f"Failed to create database {db_name}: {e}")
                continue

        try:
            conn_db = get_postgres_connection(cfg, database=db_name)
            cursor_db = conn_db.cursor()

            cursor_db.execute(schema)

            print(f"Tables ready in {db_name}")

            cursor_db.close()
            conn_db.close()

        except Exception as e:
            print(f"Failed to create tables in {db_name}: {e}")

    cursor.close()
    conn.close()


def remove_databases(engine="postgres", postgres_config=None):
    if engine != "postgres":
        raise NotImplementedError("Only PostgreSQL is supported.")

    cfg = postgres_config or DB_CONFIG

    conn = get_postgres_connection(cfg)
    cursor = conn.cursor()

    for db_name in DATABASES_POSTGRES.keys():
        try:
            cursor.execute(
                "SELECT pg_terminate_backend(pid) "
                "FROM pg_stat_activity "
                f"WHERE datname = '{db_name}' AND pid <> pg_backend_pid()"
            )
            cursor.execute(f'DROP DATABASE IF EXISTS "{db_name}"')
            print(f"Removed database: {db_name}")
        except Exception as e:
            print(f"Failed to remove {db_name}: {e}")

    cursor.close()
    conn.close()


if __name__ == "__main__":

    action = input("Action (generate/remove) [generate]: ").strip() or "generate"

    if action == "generate":
        generate_databases("postgres")
    else:
        remove_databases("postgres")