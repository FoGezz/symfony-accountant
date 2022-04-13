--
-- PostgreSQL database dump
--

-- Dumped from database version 14.0 (Debian 14.0-1.pgdg110+1)
-- Dumped by pg_dump version 14.0 (Debian 14.0-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: avg_days_on_projects(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.avg_days_on_projects() RETURNS TABLE(name character varying, avg_days integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT d.name, floor(AVG(date_part('days', (p.date_end::timestamp - p.date_beg::timestamp))))::int as avg_days
                 FROM departments d
                          JOIN projects p on d.id = p.department_id
                 GROUP BY d.name;
END;
$$;


ALTER FUNCTION public.avg_days_on_projects() OWNER TO postgres;

--
-- Name: checkbeginbeforeend(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.checkbeginbeforeend() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (NEW.date_beg > NEW.date_end) THEN
        RAISE EXCEPTION 'Begin date cannot be less then end date';
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.checkbeginbeforeend() OWNER TO postgres;

--
-- Name: checkemployeeindepartment(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.checkemployeeindepartment() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (EXISTS(SELECT * FROM departments_employees de WHERE de.department_id = NEW.department_id AND de.employee_id = NEW.employee_id) =
        TRUE) THEN
        RAISE EXCEPTION 'Employee % is already in department %', NEW.employee_id, NEW.department_id;
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.checkemployeeindepartment() OWNER TO postgres;

--
-- Name: longest_project(character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.longest_project(IN dep_name character varying, OUT longest_time integer, OUT project_name character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    SELECT p.name, (p.date_end_real::date - p.date_beg::date) as spentDays
    INTO project_name, longest_time
    FROM projects p
             JOIN departments d on p.department_id = d.id
    WHERE d.name = dep_name
      AND p.date_beg IS NOT NULL
      AND p.date_end_real IS NOT NULL
    ORDER BY spentDays DESC
    LIMIT 1;

END;
$$;


ALTER PROCEDURE public.longest_project(IN dep_name character varying, OUT longest_time integer, OUT project_name character varying) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: projects; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.projects (
    id integer NOT NULL,
    name character varying(200) NOT NULL,
    cost integer,
    department_id integer,
    date_beg date,
    date_end date,
    date_end_real date
);


ALTER TABLE public.projects OWNER TO postgres;

--
-- Name: projects_by_two_employees(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.projects_by_two_employees(emp1_id integer, emp2_id integer) RETURNS SETOF public.projects
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
        SELECT p.*
        FROM projects p
                 JOIN departments d on p.department_id = d.id
                 JOIN departments_employees de ON de.employee_id = emp1_id AND de.department_id = d.id
                 JOIN departments_employees de1 ON de1.employee_id = emp2_id AND de1.department_id = d.id;
END;
$$;


ALTER FUNCTION public.projects_by_two_employees(emp1_id integer, emp2_id integer) OWNER TO postgres;

--
-- Name: total_income(date); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.total_income(IN start_date date, OUT income integer)
    LANGUAGE plpgsql
    AS $$
DECLARE
    waste_sum              INT := 0;
    DECLARE project_salary INT := 0;
    DECLARE row            RECORD;
    DECLARE curs           refcursor;
BEGIN
    OPEN curs FOR (
        SELECT *
        FROM projects p
        WHERE p.date_beg IS NOT NULL
          AND p.date_beg >= start_date
          AND (p.date_end_real IS NULL OR p.date_end_real <= CURRENT_DATE)
    );

    income := 0;
    LOOP
        FETCH curs INTO row;
        IF NOT FOUND THEN EXIT; END IF;
        IF row.date_end_real IS NULL THEN row.date_end_real := CURRENT_DATE; END IF;

        --запишем зарплату всех сотрудников на проекте в одну переменную
        SELECT SUM(e.salary)
        INTO project_salary
        FROM departments d
                 JOIN departments_employees de on d.id = de.department_id
                 JOIN employees e on de.employee_id = e.id
        WHERE d.id = row.department_id;

        -- умножим зарплату на количество месяцев проекта и приплюсуем к сумме затрат
        waste_sum := waste_sum + (project_salary / 30) * (row.date_end_real::date - row.date_beg::date);
        income := income + row.cost - waste_sum;
        waste_sum := 0;

    END LOOP;
END;
$$;


ALTER PROCEDURE public.total_income(IN start_date date, OUT income integer) OWNER TO postgres;

--
-- Name: departments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departments (
    id integer NOT NULL,
    name character varying(20) NOT NULL
);


ALTER TABLE public.departments OWNER TO postgres;

--
-- Name: departments_employees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departments_employees (
    id integer NOT NULL,
    department_id integer NOT NULL,
    employee_id integer NOT NULL
);


ALTER TABLE public.departments_employees OWNER TO postgres;

--
-- Name: departments_employees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.departments_employees_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.departments_employees_id_seq OWNER TO postgres;

--
-- Name: departments_employees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.departments_employees_id_seq OWNED BY public.departments_employees.id;


--
-- Name: departments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.departments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.departments_id_seq OWNER TO postgres;

--
-- Name: departments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.departments_id_seq OWNED BY public.departments.id;


--
-- Name: employees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.employees (
    id integer NOT NULL,
    first_name character varying(20),
    pather_name character varying(20),
    last_name character varying(20),
    "position" character varying(50),
    salary smallint
);


ALTER TABLE public.employees OWNER TO postgres;

--
-- Name: employees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.employees_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.employees_id_seq OWNER TO postgres;

--
-- Name: employees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.employees_id_seq OWNED BY public.employees.id;


--
-- Name: employeesandprojectsbetweendate; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.employeesandprojectsbetweendate AS
 SELECT e.first_name,
    e.pather_name,
    e.last_name,
    e."position",
    d.name,
    p.name AS proj_name
   FROM (((public.employees e
     JOIN public.departments_employees de ON ((e.id = de.employee_id)))
     JOIN public.departments d ON ((de.department_id = d.id)))
     JOIN public.projects p ON ((d.id = p.department_id)))
  WHERE ((p.date_beg > '2020-05-04'::date) AND ((p.date_end_real < '2022-05-04'::date) OR (p.date_end_real IS NULL)));


ALTER TABLE public.employeesandprojectsbetweendate OWNER TO postgres;

--
-- Name: monthly_cost; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.monthly_cost AS
 SELECT p.name,
    p.cost,
    date_part('days'::text, ((p.date_end)::timestamp without time zone - (p.date_beg)::timestamp without time zone)) AS days,
    floor((((p.cost)::double precision / date_part('day'::text, ((p.date_end)::timestamp without time zone - (p.date_beg)::timestamp without time zone))) * (30)::double precision)) AS monthly_cost
   FROM public.projects p
  WHERE ((p.date_beg IS NOT NULL) AND (p.date_end IS NOT NULL) AND (p.date_end > p.date_beg));


ALTER TABLE public.monthly_cost OWNER TO postgres;

--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.projects_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO postgres;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- Name: departments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments ALTER COLUMN id SET DEFAULT nextval('public.departments_id_seq'::regclass);


--
-- Name: departments_employees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments_employees ALTER COLUMN id SET DEFAULT nextval('public.departments_employees_id_seq'::regclass);


--
-- Name: employees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employees ALTER COLUMN id SET DEFAULT nextval('public.employees_id_seq'::regclass);


--
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- Data for Name: departments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.departments (id, name) FROM stdin;
\.


--
-- Data for Name: departments_employees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.departments_employees (id, department_id, employee_id) FROM stdin;
\.


--
-- Data for Name: employees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.employees (id, first_name, pather_name, last_name, "position", salary) FROM stdin;
1	Петров	Иванович	Иван	Кассир	13000
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, name, cost, department_id, date_beg, date_end, date_end_real) FROM stdin;
\.


--
-- Name: departments_employees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.departments_employees_id_seq', 1, true);


--
-- Name: departments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.departments_id_seq', 1, false);


--
-- Name: employees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.employees_id_seq', 1, true);


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 1, false);


--
-- Name: departments_employees departments_employees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments_employees
    ADD CONSTRAINT departments_employees_pkey PRIMARY KEY (id);


--
-- Name: departments departments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (id);


--
-- Name: employees employees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (id);


--
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: projects check_begin_before_end; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_begin_before_end BEFORE UPDATE OF date_beg, date_end ON public.projects FOR EACH ROW EXECUTE FUNCTION public.checkbeginbeforeend();


--
-- Name: departments_employees check_emp_exists; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_emp_exists BEFORE INSERT ON public.departments_employees FOR EACH ROW EXECUTE FUNCTION public.checkemployeeindepartment();


--
-- Name: departments_employees fk_depts_employees_depts; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments_employees
    ADD CONSTRAINT fk_depts_employees_depts FOREIGN KEY (department_id) REFERENCES public.departments(id);


--
-- Name: departments_employees fk_depts_employees_employees; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments_employees
    ADD CONSTRAINT fk_depts_employees_employees FOREIGN KEY (employee_id) REFERENCES public.employees(id);


--
-- Name: projects fk_projects_departments; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_departments FOREIGN KEY (department_id) REFERENCES public.departments(id);


--
-- PostgreSQL database dump complete
--

