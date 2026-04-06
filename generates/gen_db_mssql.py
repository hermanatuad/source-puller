#!/usr/bin/env python3

import argparse

try:
    import pyodbc
    MSSQL_AVAILABLE = True
except Exception:
    MSSQL_AVAILABLE = False


DB_CONFIG = {
    "server": "34.45.175.24",
    "user": "sa",
    "password": "YourStrong!Password123",
    "port": 1433
}


DATABASES_MSSQL = {
    "hospital_information_system": """
        CREATE TABLE patients (
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
            created_at DATETIME2 DEFAULT GETDATE()
        );

        CREATE TABLE visits (
            visit_id VARCHAR(36) PRIMARY KEY,
            patient_id VARCHAR(36) NOT NULL,
            visit_date DATETIME2,
            exit_date DATETIME2,
            visit_type VARCHAR(30),
            attending_doctor VARCHAR(100),
            status VARCHAR(30),
            created_at DATETIME2 DEFAULT GETDATE(),
            FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        );

        CREATE TABLE services (
            service_id VARCHAR(36) PRIMARY KEY,
            service_code VARCHAR(30) UNIQUE NOT NULL,
            service_name VARCHAR(100) NOT NULL,
            service_type VARCHAR(30),
            unit_price DECIMAL(12,2),
            created_at DATETIME2 DEFAULT GETDATE()
        );

        CREATE TABLE billing (
            billing_id VARCHAR(36) PRIMARY KEY,
            visit_id VARCHAR(36) NOT NULL,
            service_id VARCHAR(36) NOT NULL,
            quantity INT,
            total_amount DECIMAL(12,2),
            billing_date DATETIME2,
            FOREIGN KEY (visit_id) REFERENCES visits(visit_id),
            FOREIGN KEY (service_id) REFERENCES services(service_id)
        );
    """
}


def get_mssql_connection(config, database=None):
    if not MSSQL_AVAILABLE:
        raise RuntimeError("pyodbc is not available. Install pyodbc first.")

    server = config["server"]
    user = config["user"]
    password = config["password"]
    port = config.get("port", 1433)

    if database:
        conn_str = (
            f"DRIVER={{ODBC Driver 17 for SQL Server}};"
            f"SERVER={server},{port};DATABASE={database};UID={user};PWD={password}"
        )
    else:
        conn_str = (
            f"DRIVER={{ODBC Driver 17 for SQL Server}};"
            f"SERVER={server},{port};UID={user};PWD={password}"
        )

    conn = pyodbc.connect(conn_str)
    conn.autocommit = True   # <-- ini yang penting
    return conn


def generate_databases(engine="mssql", mssql_config=None):
    if engine != "mssql":
        raise NotImplementedError("Only SQL Server is supported.")

    cfg = mssql_config or DB_CONFIG

    conn = get_mssql_connection(cfg)
    cursor = conn.cursor()

    for db_name, schema in DATABASES_MSSQL.items():
        try:

            cursor.execute(f"""
                IF DB_ID('{db_name}') IS NULL
                    CREATE DATABASE [{db_name}]
            """)

            print(f"Database ready: {db_name}")

            conn_db = get_mssql_connection(cfg, database=db_name)
            cursor_db = conn_db.cursor()

            for statement in schema.split(";"):
                stmt = statement.strip()
                if stmt:
                    cursor_db.execute(stmt)

            print(f"Tables created in {db_name}")

            cursor_db.close()
            conn_db.close()

        except Exception as e:
            print(f"Failed to create database {db_name}: {e}")

    cursor.close()
    conn.close()


def remove_databases(engine="mssql", mssql_config=None):
    if engine != "mssql":
        raise NotImplementedError("Only SQL Server is supported.")

    cfg = mssql_config or DB_CONFIG

    conn = get_mssql_connection(cfg)
    cursor = conn.cursor()

    for db_name in DATABASES_MSSQL.keys():
        try:
            cursor.execute(f"""
                IF DB_ID('{db_name}') IS NOT NULL
                BEGIN
                    ALTER DATABASE [{db_name}] SET SINGLE_USER WITH ROLLBACK IMMEDIATE
                    DROP DATABASE [{db_name}]
                END
            """)
            print(f"Removed database: {db_name}")
        except Exception as e:
            print(f"Failed to remove {db_name}: {e}")

    cursor.close()
    conn.close()


if __name__ == "__main__":

    action = input("Action (generate/remove) [generate]: ").strip() or "generate"

    if action == "generate":
        generate_databases("mssql")
    else:
        remove_databases("mssql")