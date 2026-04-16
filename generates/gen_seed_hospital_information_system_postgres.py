#!/usr/bin/env python3
"""
Seed script for `hospital_information_system` database.

Creates sample patients, services, visits and billing rows using simple random data.
Uses `psycopg2` for PostgreSQL.

Usage:
  python generates/gen_seed_hospital_information_system_postgres.py --host 127.0.0.1 --user postgres --password postgres --port 5432 --db hospital_information_system --patients 50
"""
import argparse
import random
import string
import uuid
from datetime import datetime, timedelta

try:
    import psycopg2
    POSTGRES_CONNECTOR = True
except Exception:
    psycopg2 = None
    POSTGRES_CONNECTOR = False

DB_CONFIG = {
    "host": "34.31.172.119",
    "user": "appuser",
    "password": "AppPass!123",
    "port": 5432
}


def get_postgres_connection(host, user, password, port, database):
    if not POSTGRES_CONNECTOR:
        raise RuntimeError('psycopg2 is not available. Install psycopg2-binary in your environment.')

    return psycopg2.connect(
        host=host,
        user=user,
        password=password,
        port=port,
        dbname=database,
    )


def rand_digits(n=8):
    return ''.join(random.choices(string.digits, k=n))


def rand_patient_id(existing_ids):
    while True:
        candidate = ''.join(random.choices(string.digits, k=5))
        if candidate not in existing_ids:
            return candidate


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

    if args.truncate:
        print('Truncating tables (billing, visits, services, patients)')
        for t in ('billing', 'visits', 'services', 'patients'):
            try:
                cursor.execute(f'DELETE FROM {t}')
            except Exception:
                print('Failed to clear table', t)

    patients = []
    generated_mrns = set()
    generated_patient_ids = set()
    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(f'Generating {args.patients} patients...')
    for i in range(1, args.patients + 1):
        patient_id = rand_patient_id(generated_patient_ids)
        generated_patient_ids.add(patient_id)
        mrn = f"MR-{rand_digits(8)}"
        while mrn in generated_mrns:
            mrn = f"MR-{rand_digits(8)}"
        generated_mrns.add(mrn)
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

        patients.append((patient_id, national_id, mrn, full_name, dob, gender, religion, marital_status, city, province, residential, race, address, now))

    patient_sql = (
        "INSERT INTO patients (patient_id, national_id, medical_record_number, full_name, date_of_birth, gender, religion, marital_status, city, province, residential, race, address, created_at)"
        " VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
    )

    print('Inserting patients...')
    cursor.executemany(patient_sql, patients)

    patient_ids = [row[0] for row in patients]

    # SERVICES
    sample_services = [
        (str(uuid.uuid4()), 'SVC-001', 'General Consultation', 'consult', 50.00),
        (str(uuid.uuid4()), 'SVC-002', 'X-Ray Chest', 'radiology', 100.00),
        (str(uuid.uuid4()), 'SVC-003', 'Complete Blood Count', 'lab', 25.00),
        (str(uuid.uuid4()), 'SVC-004', 'MRI Brain', 'radiology', 500.00),
    ]
    svc_rows = []
    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    for service_id, code, name, stype, price in sample_services:
        svc_rows.append((service_id, code, name, stype, price, now))

    print('Inserting services...')
    svc_sql = (
        "INSERT INTO services (service_id, service_code, service_name, service_type, unit_price, created_at) "
        "VALUES (%s, %s, %s, %s, %s, %s) "
        "ON CONFLICT (service_code) DO UPDATE SET "
        "service_name = EXCLUDED.service_name, "
        "service_type = EXCLUDED.service_type, "
        "unit_price = EXCLUDED.unit_price"
    )
    cursor.executemany(svc_sql, svc_rows)

    cursor.execute('SELECT service_id FROM services')
    service_ids = [row[0] for row in cursor.fetchall()]

    # VISITS and BILLING
    visit_rows = []
    billing_rows = []

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

            visit_rows.append((
                str(uuid.uuid4()),
                pid,
                visit_date.strftime('%Y-%m-%d %H:%M:%S'),
                exit_date.strftime('%Y-%m-%d %H:%M:%S'),
                visit_type,
                attending,
                status,
                created_at
            ))

    visit_sql = ("INSERT INTO visits (visit_id, patient_id, visit_date, exit_date, visit_type, attending_doctor, status, created_at) "
                 "VALUES (%s,%s,%s,%s,%s,%s,%s,%s)")
    cursor.executemany(visit_sql, visit_rows)

    visit_ids = [row[0] for row in visit_rows]

    # Billing: for each visit pick 1-3 services
    for vid in visit_ids:
        qty_count = random.randint(1, 3)
        for _ in range(qty_count):
            sid = random.choice(service_ids)
            qty = random.randint(1, 5)
            service_price = next(s[4] for s in sample_services if s[0] == sid)
            total = round(service_price * qty, 2)
            bill_date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            billing_rows.append((str(uuid.uuid4()), vid, sid, qty, total, bill_date))

    billing_sql = (
        "INSERT INTO billing (billing_id, visit_id, service_id, quantity, total_amount, billing_date) "
        "VALUES (%s,%s,%s,%s,%s,%s)"
    )
    if billing_rows:
        cursor.executemany(billing_sql, billing_rows)

    conn.commit()
    try:
        cursor.close()
    except Exception:
        pass

    print('Seeding completed.')


def main():
    parser = argparse.ArgumentParser(description='Seed hospital_information_system sample data for PostgreSQL')
    parser.add_argument('--host', default=None)
    parser.add_argument('--server', default=None, help='Alias for --host (backward compatibility)')
    parser.add_argument('--user', default=None)
    parser.add_argument('--password', default=None)
    parser.add_argument('--port', type=int, default=None)
    parser.add_argument('--db', default=None)
    parser.add_argument('--patients', type=int, default=50)
    parser.add_argument('--truncate', action='store_true', help='Truncate tables before inserting')

    args = parser.parse_args()

    # use DB_CONFIG as defaults when CLI args are not provided
    host = args.host or args.server or DB_CONFIG.get('host')
    user = args.user if args.user is not None else DB_CONFIG.get('user')
    password = args.password if args.password is not None else DB_CONFIG.get('password')
    port = args.port if args.port is not None else DB_CONFIG.get('port')
    dbname = args.db if args.db is not None else 'hospital_information_system'

    conn = get_postgres_connection(host, user, password, port, database=dbname)
    try:
        seed(conn, args)
    finally:
        try:
            conn.close()
        except Exception:
            pass


if __name__ == '__main__':
    main()
