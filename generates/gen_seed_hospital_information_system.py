#!/usr/bin/env python3
"""
Seed script for `hospital_information_system` database.

Creates sample patients, services, visits and billing rows using simple random data.
Tries to use `mysql.connector`; falls back to `pymysql` if not available.

Usage:
  python generates/gen_seed_hospital_information_system.py --host 127.0.0.1 --user root --password '' --port 3306 --db hospital_information_system --patients 50
"""
import argparse
import random
import string
import time
from datetime import datetime, timedelta

try:
    import mysql.connector as mysql_connector
    MYSQL_CONNECTOR = True
except Exception:
    mysql_connector = None
    MYSQL_CONNECTOR = False

try:
    import pymysql
    PYMysql_AVAILABLE = True
except Exception:
    pymysql = None
    PYMysql_AVAILABLE = False

DB_CONFIG = {
    "host": "34.60.27.246",
    "user": "root",
    "password": "example",
    "port": 1234
}


def get_mysql_connection(host, user, password, port, database=None):
    if MYSQL_CONNECTOR:
        cfg = {
            'host': host,
            'user': user,
            'password': password,
            'port': port,
        }
        if database:
            cfg['database'] = database
        return mysql_connector.connect(**cfg)
    if PYMysql_AVAILABLE:
        cfg = {
            'host': host,
            'user': user,
            'password': password,
            'port': port,
            'cursorclass': pymysql.cursors.DictCursor,
            'autocommit': False,
        }
        if database:
            cfg['db'] = database
        return pymysql.connect(**cfg)
    raise RuntimeError('No MySQL driver available. Install mysql-connector-python or PyMySQL.')


def rand_digits(n=8):
    return ''.join(random.choices(string.digits, k=n))


FIRST_NAMES = [
    'Ahmad', 'Muhammad', 'Abdul', 'Azlan', 'Haziq', 'Nur', 'Siti', 'Aisyah', 'Farah', 'Hannah',
    'Amir', 'Hafiz', 'Syafiq', 'Izzah', 'Roslan', 'Liyana', 'Zahid', 'Adilah', 'Maya', 'Nurul'
]
LAST_NAMES = ['Ismail', 'Hassan', 'Rahman', 'Ahmad', 'Ali', 'Tan', 'Lim', 'Wong', 'Singh', 'Raj', 'Kumar', 'Zainal', 'Othman', 'Abdul', 'Khan', 'Salleh']


def random_name(gender=None):
    if gender is None:
        gender = random.choice(['male', 'female'])
    first = random.choice(FIRST_NAMES)
    last = random.choice(LAST_NAMES)
    use_particle = random.random() < 0.6
    if use_particle:
        particle = 'bin' if gender == 'male' else 'binti'
        return f"{first} {particle} {last}"
    return f"{first} {last}"


def random_date_of_birth(min_age=0, max_age=90):
    today = datetime.now().date()
    age = random.randint(min_age, max_age)
    start = today - timedelta(days=365 * (age + 1))
    end = today - timedelta(days=365 * age)
    random_day = start + timedelta(days=random.randint(0, max(0, (end - start).days)))
    return random_day.isoformat()


def seed(conn, args):
    cursor = conn.cursor()

    db = args.db
    if db:
        try:
            # set default database
            if MYSQL_CONNECTOR:
                conn.database = db
            else:
                cursor.execute(f"USE `{db}`")
        except Exception as e:
            print('Failed to set database:', e)
            raise

    if args.truncate:
        print('Truncating tables (billing, services, visits, patients)')
        for t in ('billing', 'services', 'visits', 'patients'):
            try:
                cursor.execute(f"TRUNCATE TABLE `{t}`")
            except Exception:
                # MySQL users might not have privileges for TRUNCATE; try DELETE
                try:
                    cursor.execute(f"DELETE FROM `{t}`")
                except Exception as e:
                    print('Failed to clear table', t, e)

    patients = []
    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(f'Generating {args.patients} patients...')
    for i in range(1, args.patients + 1):
        mrn = f"MR-{i:05d}"
        national_id = rand_digits(16)
        gender = random.choice(['male', 'female'])
        full_name = random_name(gender)
        dob = random_date_of_birth(0, 90)
        religion = random.choice(['Islam', 'Christian', 'Hindu', 'Buddha', 'Other'])
        marital_status = random.choice(['single', 'married', 'divorced'])
        city = random.choice(['Kuala Lumpur', 'George Town', 'Johor Bahru', 'Kota Kinabalu', 'Kuching', 'Ipoh', 'Melaka', 'Alor Setar', 'Seremban', 'Shah Alam', 'Petaling Jaya', 'Putrajaya'])
        province = random.choice(['Selangor', 'Kuala Lumpur', 'Johor', 'Sabah', 'Sarawak', 'Penang', 'Perak', 'Melaka', 'Kedah', 'Negeri Sembilan'])
        residential = f'{random.randint(1,200)} Jalan Example'
        race = 'Asian'
        address = f'{residential}, {city}'

        patients.append((national_id, mrn, full_name, dob, gender, religion, marital_status, city, province, residential, race, address, now))

    patient_sql = (
        "INSERT INTO patients (national_id, medical_record_number, full_name, date_of_birth, gender, religion, marital_status, city, province, residential, race, address, created_at)"
        " VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
    )

    print('Inserting patients...')
    cursor.executemany(patient_sql, patients)

    # fetch patient ids
    cursor.execute('SELECT id FROM patients ORDER BY id DESC LIMIT %s' % args.patients)
    rows = cursor.fetchall()
    # rows may be tuples or dicts depending on driver
    patient_ids = []
    for r in rows[::-1]:
        if isinstance(r, dict):
            patient_ids.append(r.get('id'))
        else:
            patient_ids.append(r[0])

    if not patient_ids:
        # try selecting sequential ids starting from 1 (best effort)
        patient_ids = list(range(1, args.patients + 1))

    # SERVICES
    sample_services = [
        ('SVC-001', 'General Consultation', 'consult', 50.00),
        ('SVC-002', 'X-Ray Chest', 'radiology', 100.00),
        ('SVC-003', 'Complete Blood Count', 'lab', 25.00),
        ('SVC-004', 'MRI Brain', 'radiology', 500.00),
    ]
    svc_rows = []
    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    for code, name, stype, price in sample_services:
        svc_rows.append((code, name, stype, price, now))

    print('Inserting services...')
    svc_sql = "INSERT INTO services (service_code, service_name, service_type, unit_price, created_at) VALUES (%s,%s,%s,%s,%s)"
    cursor.executemany(svc_sql, svc_rows)

    # fetch service ids
    cursor.execute('SELECT id FROM services ORDER BY id')
    svc_fetch = cursor.fetchall()
    service_ids = []
    for r in svc_fetch:
        if isinstance(r, dict):
            service_ids.append(r.get('id'))
        else:
            service_ids.append(r[0])

    # VISITS and BILLING
    visit_rows = []
    billing_rows = []
    visit_id_seq = []

    print('Generating visits and billing...')
    for pid in patient_ids:
        num_visits = random.randint(1, 3)
        for _ in range(num_visits):
            visit_date = datetime.now() - timedelta(days=random.randint(0, 365))
            exit_date = visit_date + timedelta(hours=random.randint(1, 48))
            visit_type = random.choice(['outpatient', 'inpatient', 'emergency'])
            attending = random_name()
            status = random.choice(['finished', 'ongoing'])
            created_at = visit_date.strftime('%Y-%m-%d %H:%M:%S')

            visit_rows.append((pid, visit_date.strftime('%Y-%m-%d %H:%M:%S'), exit_date.strftime('%Y-%m-%d %H:%M:%S'), visit_type, attending, status, created_at))

    visit_sql = ("INSERT INTO visits (patient_id, visit_date, exit_date, visit_type, attending_doctor, status, created_at) "
                 "VALUES (%s,%s,%s,%s,%s,%s,%s)")
    cursor.executemany(visit_sql, visit_rows)

    # fetch visit ids
    cursor.execute('SELECT id FROM visits ORDER BY id')
    vrows = cursor.fetchall()
    visit_ids = []
    for r in vrows:
        if isinstance(r, dict):
            visit_ids.append(r.get('id'))
        else:
            visit_ids.append(r[0])

    # Billing: for each visit pick 1-3 services
    for vid in visit_ids:
        qty_count = random.randint(1, 3)
        for _ in range(qty_count):
            svc = random.choice(sample_services)
            svc_index = sample_services.index(svc)
            sid = service_ids[svc_index]
            qty = random.randint(1, 5)
            total = round(svc[3] * qty, 2)
            bill_date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            billing_rows.append((vid, sid, qty, total, bill_date))

    billing_sql = "INSERT INTO billing (visit_id, service_id, quantity, total_amount, billing_date) VALUES (%s,%s,%s,%s,%s)"
    if billing_rows:
        cursor.executemany(billing_sql, billing_rows)

    conn.commit()
    try:
        cursor.close()
    except Exception:
        pass

    print('Seeding completed.')


def main():
    parser = argparse.ArgumentParser(description='Seed hospital_information_system sample data')
    parser.add_argument('--host', default=None)
    parser.add_argument('--user', default=None)
    parser.add_argument('--password', default=None)
    parser.add_argument('--port', type=int, default=None)
    parser.add_argument('--db', default=None)
    parser.add_argument('--patients', type=int, default=50)
    parser.add_argument('--truncate', action='store_true', help='Truncate tables before inserting')

    args = parser.parse_args()

    # use DB_CONFIG as defaults when CLI args are not provided
    host = args.host if args.host is not None else DB_CONFIG.get('host')
    user = args.user if args.user is not None else DB_CONFIG.get('user')
    password = args.password if args.password is not None else DB_CONFIG.get('password')
    port = args.port if args.port is not None else DB_CONFIG.get('port')
    dbname = args.db if args.db is not None else 'hospital_information_system'

    conn = get_mysql_connection(host, user, password, port, database=dbname)
    try:
        seed(conn, args)
    finally:
        try:
            conn.close()
        except Exception:
            pass


if __name__ == '__main__':
    main()
