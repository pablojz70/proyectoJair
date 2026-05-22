SISTEMA DE VENTAS PARA VENDEDORES CON GESTIÓN DE RECETAS Y MATERIAS PRIMAS

Versión: 2.0
Fecha: Mayo 2026
Plataforma: Web (en línea)
🎯 OBJETIVO GENERAL

Desarrollar un sistema de ventas web que permita a los vendedores:

    Iniciar sesión de forma segura.

    Gestionar sus propios clientes.

    Administrar productos (simples y compuestos mediante recetas).

    Controlar inventario de materias primas.

    Registrar ventas al contado o crédito, con conversión automática de Dólar (BCV) a Bolívares.

    Consultar deudas de clientes y registrar pagos.

    Generar reportes básicos de ventas, rentabilidad y stock.

    Registrar gastos quincenales y calcular ganancias, comisiones, ahorros y dividendos.

    Gestionar empleados que producen productos y venden.

Valor agregado: Sistema de recetas que calcula automáticamente el costo de producción y descuenta materias primas al vender productos compuestos.
👥 ROLES DEL SISTEMA
Rol	Descripción
Vendedor	Acceso principal. Puede gestionar sus clientes, registrar ventas y cobros, ver sus propios reportes, gestionar sus empleados, definir comisiones y ver producción.
Empleado	Nuevo rol. Puede generar productos compuestos (usando recetas) y registrar ventas. Su pago se calcula automáticamente: bono por cada 10 productos producidos + comisión por ventas (definida por el vendedor).
Administrador	Puede ver todos los vendedores, empleados, reportes globales y gestionar permisos. Puede gestionar clientes, productos, materias primas, registrar ventas y cobros, ver reportes globales.
📱 MÓDULOS Y SUBMÓDULOS COMPLETOS
🔐 Módulo 1: Autenticación

    Login con usuario y contraseña.

    Registro de nuevo vendedor (solo administrador).

    Registro de nuevo empleado (solo vendedor o administrador).

    Recuperación de contraseña por email.

    Sesión persistente (no cerrar sesión al recargar).

👥 Módulo 2: Gestión de Clientes

    CRUD completo (Crear, Leer, Actualizar, Eliminar).

    Campos: Nombre completo, Cédula / RIF (único por vendedor), Teléfono, Observaciones.

    Búsqueda por nombre o cédula.

    Ver historial de ventas del cliente.

🧪 Módulo 3: Productos y Recetas (Núcleo del sistema)
Submódulo 3.1: Materias Primas

    CRUD de materias primas.

    Campos: Nombre, Unidad de medida, Cantidad en stock, Costo unitario (en USD).

    Ajuste manual de stock (entrada de inventario).

    Alerta visual cuando stock es bajo (ej: < 5 unidades).

Submódulo 3.2: Productos Simples

    CRUD de productos tipo "simple".

    Campos: Nombre, Descripción, Stock propio, Precio de venta (USD).

    Al vender, se descuenta el stock del producto.

Submódulo 3.3: Productos Compuestos (con Receta)

    CRUD de productos tipo "compuesto".

    Campos: Nombre, Descripción, Precio de venta (USD).

    Receta: lista de materias primas con cantidades necesarias.

    Costo de producción calculado automáticamente.

    Stock virtual: depende del stock de materias primas.

    Al vender, se valida y descuenta stock de cada materia prima.

Submódulo 3.4: Editor de Recetas

    Interfaz para asociar materias primas a un producto compuesto.

    Agregar/eliminar materias primas de la receta.

    Mostrar cálculo en tiempo real del costo de producción.

    Evitar duplicados.

    Validar que el costo de producción no sea mayor al precio de venta (advertencia).

👷 Módulo 4: Gestión de Empleados (Nuevo)
Submódulo 4.1: Registrar Empleado

    Asignar empleado a un vendedor.

    Campos: Nombre completo, correo, teléfono, comisión por venta (%), bono por cada 10 unidades producidas (USD).

    Activar/desactivar empleado.

Submódulo 4.2: Producción de Productos (Empleado)

    El empleado selecciona un producto compuesto.

    Ingresa cantidad a producir.

    El sistema valida disponibilidad de materias primas.

    Al producir, se descuenta stock de materias primas.

    Se registra la producción asociada al empleado.

    Cada 10 unidades producidas genera automáticamente un bono para el empleado.

Submódulo 4.3: Reporte de Producción

    Ver producción por empleado (día, semana, mes).

    Ver bonos generados y comisiones devengadas.

    Cálculo automático de pago al empleado al cierre quincenal.

💰 Módulo 5: Ventas
Submódulo 5.1: Registrar Venta

    Seleccionar cliente.

    Seleccionar producto (solo con stock disponible).

    Ingresar cantidad.

    Mostrar precio unitario (USD).

    Calcular subtotal y total.

    Conversión a bolívares (API MonitorDólar + tasa manual).

    Seleccionar tipo: Contado o Crédito (con fecha de vencimiento).

    Confirmar venta: valida stock, descuenta stock (simple o materias primas), guarda venta.

    Si la venta la realiza un empleado, se le asigna su comisión automáticamente.

Submódulo 5.2: Historial de Ventas

    Lista de todas las ventas del vendedor (o global para admin).

    Filtros por fecha, cliente, tipo, pagado/pendiente, empleado que vendió.

    Ver detalle de cada venta.

💸 Módulo 6: Pagos y Cobranzas
Submódulo 6.1: Consultar Deuda de Cliente

    Buscar cliente.

    Mostrar ventas a crédito no pagadas, monto total adeudado (USD y Bs), fechas de vencimiento.

Submódulo 6.2: Registrar Pago

    Seleccionar cliente.

    Seleccionar una o varias ventas a crédito.

    Ingresar monto pagado (USD o Bs con conversión).

    Guardar pago y actualizar estado de ventas (total o parcial).

📊 Módulo 7: Reportes
Submódulo 7.1: Ventas

    Ventas del día / semana / mes (totales en USD y Bs).

    Ventas por tipo (contado vs crédito).

    Ventas por cliente (top 5).

    Ventas por empleado.

Submódulo 7.2: Rentabilidad

    Productos más vendidos (cantidad).

    Productos más rentables (precio venta - costo producción).

    Margen de ganancia promedio.

Submódulo 7.3: Inventario

    Stock actual de materias primas.

    Stock actual de productos simples.

    Alertas de stock bajo.

    Productos compuestos que no se pueden producir por falta de materias primas.

Submódulo 7.4: Cobranzas

    Clientes morosos (deuda vencida > 0).

    Pagos registrados por período.

    Eficiencia de cobranza (total cobrado / total facturado a crédito).

Submódulo 7.5: Finanzas Quincenales (Nuevo)

    Registro quincenal de gastos:

        Desglose de gastos por materia prima (compras de inventario).

        Pagos a empleados (bonos de producción + comisiones).

        Otros gastos (alquiler, servicios, transporte, etc.).

    Cálculo automático de ganancias:

        Ganancias netas = Ventas totales del período - Costo total de producción - Gastos registrados.

    Distribución de ganancias:

        Comisión del socio: 10% de las ganancias netas.

        Ahorros (caja chica): monto definido por el vendedor.

        Dividendos: monto a repartir.

        Otros: concepto libre (ej: reinversión, fondo de emergencia).

    Cierre quincenal: guarda histórico, no se puede modificar después de cerrado.

    Reporte de finanzas: visualización por quincena, comparativa entre períodos.
