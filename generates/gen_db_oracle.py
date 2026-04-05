#!/usr/bin/env python3

import argparse

try:
    import oracledb
    ORACLE_AVAILABLE = True
except Exception:
    ORACLE_AVAILABLE = False


# Default Oracle server config (can be overridden via CLI)
DB_CONFIG = {
    "host": "34.60.27.246",
    "port": 3333,
    "user": "app_user",
    "password": "app_pass",
    "service_name": "FREEPDB1",
}

SYSTEM_TABLES = {
    "hospital_information_system": {
        "prefix": "his_",
        "ddl": [
            """
            CREATE TABLE {prefix}patients (
                patient_id VARCHAR2(36) PRIMARY KEY,
                national_id VARCHAR2(50) UNIQUE,
                medical_record_number VARCHAR2(50) UNIQUE NOT NULL,
                full_name VARCHAR2(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR2(10),
                religion VARCHAR2(10),
                marital_status VARCHAR2(20),
                city VARCHAR2(50),
                province VARCHAR2(50),
                residential VARCHAR2(100),
                race VARCHAR2(20),
                address CLOB,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}visits (
                visit_id VARCHAR2(36) PRIMARY KEY,
                patient_id VARCHAR2(36) NOT NULL,
                visit_date TIMESTAMP,
                exit_date TIMESTAMP,
                visit_type VARCHAR2(30),
                attending_doctor VARCHAR2(100),
                status VARCHAR2(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}visits_fk_patient FOREIGN KEY (patient_id)
                    REFERENCES {prefix}patients(patient_id)
            )
            """,
            """
            CREATE TABLE {prefix}services (
                service_id VARCHAR2(36) PRIMARY KEY,
                service_code VARCHAR2(30) UNIQUE NOT NULL,
                service_name VARCHAR2(100) NOT NULL,
                service_type VARCHAR2(30),
                unit_price NUMBER(12,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}billing (
                billing_id VARCHAR2(36) PRIMARY KEY,
                visit_id VARCHAR2(36) NOT NULL,
                service_id VARCHAR2(36) NOT NULL,
                quantity NUMBER(10),
                total_amount NUMBER(12,2),
                billing_date TIMESTAMP,
                CONSTRAINT {prefix}billing_fk_visit FOREIGN KEY (visit_id)
                    REFERENCES {prefix}visits(visit_id),
                CONSTRAINT {prefix}billing_fk_service FOREIGN KEY (service_id)
                    REFERENCES {prefix}services(service_id)
            )
            """,
        ],
        "drop_order": ["billing", "services", "visits", "patients"],
    },
    "his02": {
        "prefix": "his02_",
        "ddl": [
            """
            CREATE TABLE {prefix}patients (
                patient_id VARCHAR2(36) PRIMARY KEY,
                national_id VARCHAR2(50) UNIQUE,
                medical_record_number VARCHAR2(50) UNIQUE NOT NULL,
                full_name VARCHAR2(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR2(10),
                religion VARCHAR2(10),
                marital_status VARCHAR2(20),
                city VARCHAR2(50),
                province VARCHAR2(50),
                residential VARCHAR2(100),
                race VARCHAR2(20),
                address CLOB,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}visits (
                visit_id VARCHAR2(36) PRIMARY KEY,
                patient_id VARCHAR2(36) NOT NULL,
                visit_date TIMESTAMP,
                exit_date TIMESTAMP,
                visit_type VARCHAR2(30),
                attending_doctor VARCHAR2(100),
                status VARCHAR2(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}visits_fk_patient FOREIGN KEY (patient_id)
                    REFERENCES {prefix}patients(patient_id)
            )
            """,
            """
            CREATE TABLE {prefix}services (
                service_id VARCHAR2(36) PRIMARY KEY,
                service_code VARCHAR2(30) UNIQUE NOT NULL,
                service_name VARCHAR2(100) NOT NULL,
                service_type VARCHAR2(30),
                unit_price NUMBER(12,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}billing (
                billing_id VARCHAR2(36) PRIMARY KEY,
                visit_id VARCHAR2(36) NOT NULL,
                service_id VARCHAR2(36) NOT NULL,
                quantity NUMBER(10),
                total_amount NUMBER(12,2),
                billing_date TIMESTAMP,
                CONSTRAINT {prefix}billing_fk_visit FOREIGN KEY (visit_id)
                    REFERENCES {prefix}visits(visit_id),
                CONSTRAINT {prefix}billing_fk_service FOREIGN KEY (service_id)
                    REFERENCES {prefix}services(service_id)
            )
            """,
        ],
        "drop_order": ["billing", "services", "visits", "patients"],
    },
    "laboratory_information_system": {
        "prefix": "lis_",
        "ddl": [
            """
            CREATE TABLE {prefix}patients (
                patient_id VARCHAR2(36) PRIMARY KEY,
                medical_record_number VARCHAR2(50) UNIQUE NOT NULL,
                full_name VARCHAR2(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR2(10),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}lab_orders (
                lab_order_id VARCHAR2(36) PRIMARY KEY,
                patient_id VARCHAR2(36) NOT NULL,
                order_date TIMESTAMP,
                ordering_doctor VARCHAR2(100),
                status VARCHAR2(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}lab_orders_fk_patient FOREIGN KEY (patient_id)
                    REFERENCES {prefix}patients(patient_id)
            )
            """,
            """
            CREATE TABLE {prefix}lab_tests (
                lab_test_id VARCHAR2(36) PRIMARY KEY,
                test_code VARCHAR2(30) UNIQUE NOT NULL,
                test_name VARCHAR2(100) NOT NULL,
                unit VARCHAR2(20),
                reference_range VARCHAR2(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}lab_results (
                lab_result_id VARCHAR2(36) PRIMARY KEY,
                lab_order_id VARCHAR2(36) NOT NULL,
                lab_test_id VARCHAR2(36) NOT NULL,
                result_value VARCHAR2(50),
                result_flag VARCHAR2(20),
                result_date TIMESTAMP,
                CONSTRAINT {prefix}lab_results_fk_order FOREIGN KEY (lab_order_id)
                    REFERENCES {prefix}lab_orders(lab_order_id),
                CONSTRAINT {prefix}lab_results_fk_test FOREIGN KEY (lab_test_id)
                    REFERENCES {prefix}lab_tests(lab_test_id)
            )
            """,
        ],
        "drop_order": ["lab_results", "lab_tests", "lab_orders", "patients"],
    },
    "radiology_information_system": {
        "prefix": "ris_",
        "ddl": [
            """
            CREATE TABLE {prefix}patients (
                patient_id VARCHAR2(36) PRIMARY KEY,
                medical_record_number VARCHAR2(50) UNIQUE NOT NULL,
                full_name VARCHAR2(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR2(10),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}radiology_orders (
                radiology_order_id VARCHAR2(36) PRIMARY KEY,
                patient_id VARCHAR2(36) NOT NULL,
                order_date TIMESTAMP,
                ordering_doctor VARCHAR2(100),
                modality VARCHAR2(20),
                status VARCHAR2(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}orders_fk_patient FOREIGN KEY (patient_id)
                    REFERENCES {prefix}patients(patient_id)
            )
            """,
            """
            CREATE TABLE {prefix}imaging_studies (
                imaging_study_id VARCHAR2(36) PRIMARY KEY,
                radiology_order_id VARCHAR2(36) NOT NULL,
                study_date TIMESTAMP,
                body_part VARCHAR2(50),
                image_location CLOB,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}studies_fk_order FOREIGN KEY (radiology_order_id)
                    REFERENCES {prefix}radiology_orders(radiology_order_id)
            )
            """,
            """
            CREATE TABLE {prefix}radiology_reports (
                radiology_report_id VARCHAR2(36) PRIMARY KEY,
                imaging_study_id VARCHAR2(36) NOT NULL,
                radiologist_name VARCHAR2(100),
                findings CLOB,
                impression CLOB,
                report_date TIMESTAMP,
                CONSTRAINT {prefix}reports_fk_study FOREIGN KEY (imaging_study_id)
                    REFERENCES {prefix}imaging_studies(imaging_study_id)
            )
            """,
        ],
        "drop_order": ["radiology_reports", "imaging_studies", "radiology_orders", "patients"],
    },
    "datawarehouse": {
        "prefix": "dw_",
        "ddl": [
            """
            CREATE TABLE {prefix}patients (
                patient_id VARCHAR2(36) PRIMARY KEY,
                medical_record_number VARCHAR2(50) UNIQUE NOT NULL,
                full_name VARCHAR2(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR2(10),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE TABLE {prefix}radiology_orders (
                radiology_order_id VARCHAR2(36) PRIMARY KEY,
                patient_id VARCHAR2(36) NOT NULL,
                order_date TIMESTAMP,
                ordering_doctor VARCHAR2(100),
                modality VARCHAR2(20),
                status VARCHAR2(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}orders_fk_patient FOREIGN KEY (patient_id)
                    REFERENCES {prefix}patients(patient_id)
            )
            """,
            """
            CREATE TABLE {prefix}imaging_studies (
                imaging_study_id VARCHAR2(36) PRIMARY KEY,
                radiology_order_id VARCHAR2(36) NOT NULL,
                study_date TIMESTAMP,
                body_part VARCHAR2(50),
                image_location CLOB,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT {prefix}studies_fk_order FOREIGN KEY (radiology_order_id)
                    REFERENCES {prefix}radiology_orders(radiology_order_id)
            )
            """,
            """
            CREATE TABLE {prefix}radiology_reports (
                radiology_report_id VARCHAR2(36) PRIMARY KEY,
                imaging_study_id VARCHAR2(36) NOT NULL,
                radiologist_name VARCHAR2(100),
                findings CLOB,
                impression CLOB,
                report_date TIMESTAMP,
                CONSTRAINT {prefix}reports_fk_study FOREIGN KEY (imaging_study_id)
                    REFERENCES {prefix}imaging_studies(imaging_study_id)
            )
            """,
        ],
        "drop_order": ["radiology_reports", "imaging_studies", "radiology_orders", "patients"],
    },
}


def get_oracle_connection(config):
    if not ORACLE_AVAILABLE:
        raise RuntimeError("oracledb is not available. Install: pip install oracledb")

    cfg = config.copy()
    dsn = cfg.get("dsn")
    if not dsn:
        service_name = cfg.get("service_name")
        if not service_name:
            raise ValueError("Oracle service_name is required (or provide --dsn).")
        dsn = oracledb.makedsn(cfg["host"], cfg["port"], service_name=service_name)

    return oracledb.connect(
        user=cfg["user"],
        password=cfg["password"],
        dsn=dsn,
    )


def oracle_error_code(error):
    try:
        return error.args[0].code
    except Exception:
        return None


def generate_databases(engine='oracle', oracle_config=None):
    if engine != 'oracle':
        raise NotImplementedError('Only Oracle generation is implemented by this script.')

    cfg = oracle_config or DB_CONFIG
    conn = None
    cursor = None
    try:
        conn = get_oracle_connection(cfg)
        cursor = conn.cursor()

        for system_name, definition in SYSTEM_TABLES.items():
            prefix = definition["prefix"]
            try:
                for raw_stmt in definition["ddl"]:
                    stmt = raw_stmt.format(prefix=prefix).strip()
                    try:
                        cursor.execute(stmt)
                    except Exception as create_error:
                        if oracle_error_code(create_error) == 955:
                            print(f"Table already exists, skipped: {create_error}")
                            continue
                        raise
                print(f"Created Oracle tables for: {system_name}")
            except Exception as e:
                print(f"Failed creating tables for {system_name}: {e}")

        conn.commit()
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


def remove_databases(engine='oracle', oracle_config=None):
    if engine != 'oracle':
        raise NotImplementedError('Only Oracle removal is implemented by this script.')

    cfg = oracle_config or DB_CONFIG
    conn = None
    cursor = None
    try:
        conn = get_oracle_connection(cfg)
        cursor = conn.cursor()

        for system_name, definition in SYSTEM_TABLES.items():
            prefix = definition["prefix"]
            for table_suffix in definition["drop_order"]:
                table_name = f"{prefix}{table_suffix}"
                try:
                    cursor.execute(f"DROP TABLE {table_name} CASCADE CONSTRAINTS")
                    print(f"Dropped table: {table_name}")
                except Exception as e:
                    if oracle_error_code(e) == 942:
                        continue
                    print(f"Failed dropping table {table_name}: {e}")
            print(f"Cleanup done for: {system_name}")

        conn.commit()
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


if __name__ == "__main__":
    import sys

    def interactive_mode():
        print('Interactive mode — simple input/output')
        action = input('Action (generate/remove) [generate]: ').strip() or 'generate'
        while action not in ('generate', 'remove'):
            action = input('Please enter "generate" or "remove": ').strip()

        engine = input('Engine (oracle) [oracle]: ').strip() or 'oracle'
        while engine not in ('oracle',):
            engine = input('Please enter "oracle": ').strip()

        oracle_cfg = DB_CONFIG.copy()

        if engine == 'oracle':
            host = input(f'Oracle host [{oracle_cfg["host"]}]: ').strip()
            if host:
                oracle_cfg['host'] = host
            user = input(f'Oracle user [{oracle_cfg["user"]}]: ').strip()
            if user:
                oracle_cfg['user'] = user
            password = input(f'Oracle password [{oracle_cfg["password"]}]: ').strip()
            if password:
                oracle_cfg['password'] = password
            port = input(f'Oracle port [{oracle_cfg["port"]}]: ').strip()
            if port:
                try:
                    oracle_cfg['port'] = int(port)
                except ValueError:
                    print('Invalid port, using default')
            service_name = input(f'Oracle service_name [{oracle_cfg["service_name"]}]: ').strip()
            if service_name:
                oracle_cfg['service_name'] = service_name

        # execute chosen action
        try:
            if action == 'generate':
                generate_databases(engine, oracle_config=oracle_cfg)
            else:
                remove_databases(engine, oracle_config=oracle_cfg)
        except Exception as e:
            print('Error:', e)

    # If no args provided, run interactive simple I/O mode
    if len(sys.argv) == 1:
        interactive_mode()
    else:
        parser = argparse.ArgumentParser(description='Generate or remove example Oracle tables')
        parser.add_argument('command', choices=['generate', 'remove'], help='Action to perform')
        parser.add_argument('--engine', choices=['oracle'], default='oracle', help='Database engine to use')
        parser.add_argument('--host', help='Database host (overrides config)')
        parser.add_argument('--user', help='Database user (overrides config)')
        parser.add_argument('--password', help='Database password (overrides config)')
        parser.add_argument('--port', type=int, help='Database port (overrides config)')
        parser.add_argument('--service-name', help='Oracle service name (overrides config)')
        parser.add_argument('--dsn', help='Oracle DSN, e.g. host:port/service_name (overrides host/port/service-name)')

        args = parser.parse_args()

        oracle_cfg = DB_CONFIG.copy()
        
        if args.host:
            oracle_cfg['host'] = args.host
        if args.user:
            oracle_cfg['user'] = args.user
        if args.password:
            oracle_cfg['password'] = args.password
        if args.port:
            oracle_cfg['port'] = args.port
        if args.service_name:
            oracle_cfg['service_name'] = args.service_name
        if args.dsn:
            oracle_cfg['dsn'] = args.dsn

        if args.command == 'generate':
            generate_databases(args.engine, oracle_config=oracle_cfg)
        else:
            remove_databases(args.engine, oracle_config=oracle_cfg)
