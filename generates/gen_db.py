#!/usr/bin/env python3

import os
import argparse
import sqlite3
from pathlib import Path

try:
    import mysql.connector
    MYSQL_AVAILABLE = True
except Exception:
    MYSQL_AVAILABLE = False


# Default MySQL server config (can be overridden via CLI)
DB_CONFIG = {
    "host": "34.68.68.86",
    "user": "root",
    "password": "example",
    "port": 1234
}

# MySQL schemas (keeps MySQL-specific syntax)
DATABASES_MYSQL = {
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
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE visits (
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

        CREATE TABLE services (
            service_id VARCHAR(36) PRIMARY KEY,
            service_code VARCHAR(30) UNIQUE NOT NULL,
            service_name VARCHAR(100) NOT NULL,
            service_type VARCHAR(30),
            unit_price DECIMAL(12,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE billing (
            billing_id VARCHAR(36) PRIMARY KEY,
            visit_id VARCHAR(36) NOT NULL,
            service_id VARCHAR(36) NOT NULL,
            quantity INT,
            total_amount DECIMAL(12,2),
            billing_date TIMESTAMP,
            FOREIGN KEY (visit_id) REFERENCES visits(visit_id),
            FOREIGN KEY (service_id) REFERENCES services(service_id)
        );
    """,

    # "his02": """
    #     CREATE TABLE patients (
    #         patient_id VARCHAR(36) PRIMARY KEY,
    #         national_id VARCHAR(50) UNIQUE,
    #         medical_record_number VARCHAR(50) UNIQUE NOT NULL,
    #         full_name VARCHAR(100) NOT NULL,
    #         date_of_birth DATE,
    #         gender VARCHAR(10),
    #         religion VARCHAR(10),
    #         marital_status VARCHAR(20),
    #         city VARCHAR(50),
    #         province VARCHAR(50),
    #         residential VARCHAR(100),
    #         race VARCHAR(20),
    #         address TEXT,
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE visits (
    #         visit_id VARCHAR(36) PRIMARY KEY,
    #         patient_id VARCHAR(36) NOT NULL,
    #         visit_date TIMESTAMP,
    #         exit_date TIMESTAMP,
    #         visit_type VARCHAR(30),
    #         attending_doctor VARCHAR(100),
    #         status VARCHAR(30),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
    #     );

    #     CREATE TABLE services (
    #         service_id VARCHAR(36) PRIMARY KEY,
    #         service_code VARCHAR(30) UNIQUE NOT NULL,
    #         service_name VARCHAR(100) NOT NULL,
    #         service_type VARCHAR(30),
    #         unit_price DECIMAL(12,2),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE billing (
    #         billing_id VARCHAR(36) PRIMARY KEY,
    #         visit_id VARCHAR(36) NOT NULL,
    #         service_id VARCHAR(36) NOT NULL,
    #         quantity INT,
    #         total_amount DECIMAL(12,2),
    #         billing_date TIMESTAMP,
    #         FOREIGN KEY (visit_id) REFERENCES visits(visit_id),
    #         FOREIGN KEY (service_id) REFERENCES services(service_id)
    #     );
    # """,

    # "laboratory_information_system": """
    #     CREATE TABLE patients (
    #         patient_id VARCHAR(36) PRIMARY KEY,
    #         medical_record_number VARCHAR(50) UNIQUE NOT NULL,
    #         full_name VARCHAR(100) NOT NULL,
    #         date_of_birth DATE,
    #         gender VARCHAR(10),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE lab_orders (
    #         lab_order_id VARCHAR(36) PRIMARY KEY,
    #         patient_id VARCHAR(36) NOT NULL,
    #         order_date TIMESTAMP,
    #         ordering_doctor VARCHAR(100),
    #         status VARCHAR(30),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
    #     );

    #     CREATE TABLE lab_tests (
    #         lab_test_id VARCHAR(36) PRIMARY KEY,
    #         test_code VARCHAR(30) UNIQUE NOT NULL,
    #         test_name VARCHAR(100) NOT NULL,
    #         unit VARCHAR(20),
    #         reference_range VARCHAR(50),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE lab_results (
    #         lab_result_id VARCHAR(36) PRIMARY KEY,
    #         lab_order_id VARCHAR(36) NOT NULL,
    #         lab_test_id VARCHAR(36) NOT NULL,
    #         result_value VARCHAR(50),
    #         result_flag VARCHAR(20),
    #         result_date TIMESTAMP,
    #         FOREIGN KEY (lab_order_id) REFERENCES lab_orders(lab_order_id),
    #         FOREIGN KEY (lab_test_id) REFERENCES lab_tests(lab_test_id)
    #     );
    # """,

    # "radiology_information_system": """
    #     CREATE TABLE patients (
    #         patient_id VARCHAR(36) PRIMARY KEY,
    #         medical_record_number VARCHAR(50) UNIQUE NOT NULL,
    #         full_name VARCHAR(100) NOT NULL,
    #         date_of_birth DATE,
    #         gender VARCHAR(10),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE radiology_orders (
    #         radiology_order_id VARCHAR(36) PRIMARY KEY,
    #         patient_id VARCHAR(36) NOT NULL,
    #         order_date TIMESTAMP,
    #         ordering_doctor VARCHAR(100),
    #         modality VARCHAR(20),
    #         status VARCHAR(30),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
    #     );

    #     CREATE TABLE imaging_studies (
    #         imaging_study_id VARCHAR(36) PRIMARY KEY,
    #         radiology_order_id VARCHAR(36) NOT NULL,
    #         study_date TIMESTAMP,
    #         body_part VARCHAR(50),
    #         image_location TEXT,
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (radiology_order_id) REFERENCES radiology_orders(radiology_order_id)
    #     );

    #     CREATE TABLE radiology_reports (
    #         radiology_report_id VARCHAR(36) PRIMARY KEY,
    #         imaging_study_id VARCHAR(36) NOT NULL,
    #         radiologist_name VARCHAR(100),
    #         findings TEXT,
    #         impression TEXT,
    #         report_date TIMESTAMP,
    #         FOREIGN KEY (imaging_study_id) REFERENCES imaging_studies(imaging_study_id)
    #     );
    # """,

    # "datawarehouse": """
    #     CREATE TABLE patients (
    #         patient_id VARCHAR(36) PRIMARY KEY,
    #         medical_record_number VARCHAR(50) UNIQUE NOT NULL,
    #         full_name VARCHAR(100) NOT NULL,
    #         date_of_birth DATE,
    #         gender VARCHAR(10),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    #     );

    #     CREATE TABLE radiology_orders (
    #         radiology_order_id VARCHAR(36) PRIMARY KEY,
    #         patient_id VARCHAR(36) NOT NULL,
    #         order_date TIMESTAMP,
    #         ordering_doctor VARCHAR(100),
    #         modality VARCHAR(20),
    #         status VARCHAR(30),
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
    #     );

    #     CREATE TABLE imaging_studies (
    #         imaging_study_id VARCHAR(36) PRIMARY KEY,
    #         radiology_order_id VARCHAR(36) NOT NULL,
    #         study_date TIMESTAMP,
    #         body_part VARCHAR(50),
    #         image_location TEXT,
    #         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #         FOREIGN KEY (radiology_order_id) REFERENCES radiology_orders(radiology_order_id)
    #     );

    #     CREATE TABLE radiology_reports (
    #         radiology_report_id VARCHAR(36) PRIMARY KEY,
    #         imaging_study_id VARCHAR(36) NOT NULL,
    #         radiologist_name VARCHAR(100),
    #         findings TEXT,
    #         impression TEXT,
    #         report_date TIMESTAMP,
    #         FOREIGN KEY (imaging_study_id) REFERENCES imaging_studies(imaging_study_id)
    #     );
    # """
}

def get_mysql_connection(config, database=None):
    if not MYSQL_AVAILABLE:
        raise RuntimeError("mysql.connector is not available. Install mysql-connector-python in your environment.")
    cfg = config.copy()
    if database:
        cfg["database"] = database
    return mysql.connector.connect(**cfg)


def generate_databases(engine='mysql', mysql_config=None):
    if engine != 'mysql':
        raise NotImplementedError('Only MySQL generation is implemented by this script.')

    cfg = mysql_config or DB_CONFIG
    conn = None
    cursor = None
    try:
        conn = get_mysql_connection(cfg)
        cursor = conn.cursor()
        for db_name, schema in DATABASES_MYSQL.items():
            try:
                cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{db_name}`")
                conn.database = db_name

                for statement in schema.split(";"):
                    stmt = statement.strip()
                    if stmt:
                        cursor.execute(stmt)

                print(f"Created database and tables: {db_name}")
            except Exception as e:
                print(f"Failed to create MySQL database {db_name}: {e}")
        conn.commit()
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


def remove_databases(engine='mysql', mysql_config=None):
    if engine != 'mysql':
        raise NotImplementedError('Only MySQL removal is implemented by this script.')

    cfg = mysql_config or DB_CONFIG
    conn = None
    cursor = None
    try:
        conn = get_mysql_connection(cfg)
        cursor = conn.cursor()

        for db_name in DATABASES_MYSQL.keys():
            try:
                cursor.execute(f"DROP DATABASE IF EXISTS `{db_name}`")
                print(f"Removed MySQL database: {db_name}")
            except Exception as e:
                print(f"Failed to drop MySQL database {db_name}: {e}")
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

        engine = input('Engine (mysql) [mysql]: ').strip() or 'mysql'
        while engine not in ('mysql',):
            engine = input('Please enter "mysql": ').strip()

        mysql_cfg = DB_CONFIG.copy()

        if engine == 'mysql':
            host = input(f'MySQL host [{mysql_cfg["host"]}]: ').strip()
            if host:
                mysql_cfg['host'] = host
            user = input(f'MySQL user [{mysql_cfg["user"]}]: ').strip()
            if user:
                mysql_cfg['user'] = user
            password = input(f'MySQL password [{mysql_cfg["password"]}]: ').strip()
            if password:
                mysql_cfg['password'] = password
            port = input(f'MySQL port [{mysql_cfg["port"]}]: ').strip()
            if port:
                try:
                    mysql_cfg['port'] = int(port)
                except ValueError:
                    print('Invalid port, using default')

        # execute chosen action
        try:
            if action == 'generate':
                generate_databases(engine, mysql_config=mysql_cfg)
            else:
                remove_databases(engine, mysql_config=mysql_cfg)
        except Exception as e:
            print('Error:', e)

    # If no args provided, run interactive simple I/O mode
    if len(sys.argv) == 1:
        interactive_mode()
    else:
        parser = argparse.ArgumentParser(description='Generate or remove example databases (MySQL, PostgreSQL, or SQLite)')
        parser.add_argument('command', choices=['generate', 'remove'], help='Action to perform')
        parser.add_argument('--engine', choices=['mysql', 'postgres', 'sqlite'], default='mysql', help='Database engine to use')
        parser.add_argument('--sqlite-dir', default='.', help='Directory for SQLite DB files')
        parser.add_argument('--host', help='Database host (overrides config)')
        parser.add_argument('--user', help='Database user (overrides config)')
        parser.add_argument('--password', help='Database password (overrides config)')
        parser.add_argument('--port', type=int, help='Database port (overrides config)')

        args = parser.parse_args()

        mysql_cfg = DB_CONFIG.copy()
        
        if args.host:
            mysql_cfg['host'] = args.host
        if args.user:
            mysql_cfg['user'] = args.user
        if args.password:
            mysql_cfg['password'] = args.password
        if args.port:
            mysql_cfg['port'] = args.port

        if args.command == 'generate':
            generate_databases(args.engine, mysql_config=mysql_cfg)
        else:
            remove_databases(args.engine, mysql_config=mysql_cfg)
