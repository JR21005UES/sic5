<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('catalogo')->insert([
            // Cuentas de Activo
            ['codigo' => 1, 'nombre' => 'ACTIVO', 'descripcion' => 'Cuenta de activo', 'naturaleza_id' => 1],
            ['codigo' => 11, 'nombre' => 'CORRIENTE', 'descripcion' => 'Activo corriente', 'naturaleza_id' => 1],
            ['codigo' => 1101, 'nombre' => 'EFECTIVO Y EQUIVALENTES DE EFECTIVO', 'descripcion' => 'Cuenta de efectivo', 'naturaleza_id' => 1],
            ['codigo' => 1102, 'nombre' => 'INVERSIONES FINANCIERAS A CORTO PLAZO', 'descripcion' => 'Inversiones a corto plazo', 'naturaleza_id' => 1],
            ['codigo' => 1103, 'nombre' => 'CUENTAS Y DOCUMENTOS POR COBRAR', 'descripcion' => 'Cuentas y documentos pendientes de cobro', 'naturaleza_id' => 1],
            ['codigo' => 1104, 'nombre' => 'R ESTIMACION PARA CUENTAS INCOBRABLES', 'descripcion' => 'Estimación para cuentas incobrables', 'naturaleza_id' => 2],
            ['codigo' => 1105, 'nombre' => 'DOCUMENTOS POR COBRAR', 'descripcion' => 'Documentos pendientes de cobro', 'naturaleza_id' => 1],
            ['codigo' => 1106, 'nombre' => 'LLAMAMIENTOS DE CAPITAL', 'descripcion' => 'Llamamientos de capital', 'naturaleza_id' => 1],
            ['codigo' => 1107, 'nombre' => 'PRESTAMOS A EMPLEADOS Y ACCIONISTAS', 'descripcion' => 'Préstamos a empleados y accionistas', 'naturaleza_id' => 1],
            ['codigo' => 1108, 'nombre' => 'OTRAS CUENTAS POR COBRAR', 'descripcion' => 'Otras cuentas pendientes de cobro', 'naturaleza_id' => 1],
            ['codigo' => 1109, 'nombre' => 'INVENTARIOS', 'descripcion' => 'Inventarios de bienes', 'naturaleza_id' => 1],
            ['codigo' => 1110, 'nombre' => 'R ESTIMACION POR DETERIORO VALOREN LOS INVENTARIOS', 'descripcion' => 'Estimación de deterioro de inventarios', 'naturaleza_id' => 2],
            ['codigo' => 1111, 'nombre' => 'GASTOS PAGADOS POR ANTICIPADO', 'descripcion' => 'Gastos pagados anticipadamente', 'naturaleza_id' => 1],
            ['codigo' => 1112, 'nombre' => 'IVA CREDITO FISCAL', 'descripcion' => 'Crédito fiscal por IVA', 'naturaleza_id' => 1],
            ['codigo' => 1113, 'nombre' => 'IVA PAGADO POR ANTICIPADO', 'descripcion' => 'IVA pagado anticipadamente', 'naturaleza_id' => 1],
            ['codigo' => 1114, 'nombre' => 'REMANTE DE CREDITO FISCAL', 'descripcion' => 'Remanente de crédito fiscal', 'naturaleza_id' => 1],
            ['codigo' => 12, 'nombre' => 'ACTIVOS NO CORRIENTES', 'descripcion' => 'Activos no corrientes', 'naturaleza_id' => 1],
            ['codigo' => 1201, 'nombre' => 'PROPIEDAD, PLANTA Y EQUIPO', 'descripcion' => 'Propiedad, planta y equipo de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 1202, 'nombre' => 'R DEPRECIACIONES', 'descripcion' => 'Depreciaciones acumuladas', 'naturaleza_id' => 2],
            ['codigo' => 1203, 'nombre' => 'R DETERIORO DE VALOR DE PROPIEDAD PLANTA Y EQUIPO', 'descripcion' => 'Deterioro de valor de activos fijos', 'naturaleza_id' => 2],
            ['codigo' => 1204, 'nombre' => 'INTANGIBLES', 'descripcion' => 'Activos intangibles', 'naturaleza_id' => 1],
            ['codigo' => 1205, 'nombre' => 'R AMORTIZACION DE INTANGIBLES', 'descripcion' => 'Amortización de intangibles', 'naturaleza_id' => 2],
            ['codigo' => 1207, 'nombre' => 'INVERSIONES EN ASOCIADAS', 'descripcion' => 'Inversiones en empresas asociadas', 'naturaleza_id' => 1],
            ['codigo' => 1208, 'nombre' => 'ACTIVO POR IMPUESTOS SOBRE LA RENTA DIFERIDO', 'descripcion' => 'Activo por impuestos diferidos', 'naturaleza_id' => 1],
            
            // Cuentas de Pasivo
            ['codigo' => 2, 'nombre' => 'PASIVO', 'descripcion' => 'Cuenta de pasivo', 'naturaleza_id' => 3],
            ['codigo' => 21, 'nombre' => 'CORRIENTE', 'descripcion' => 'Pasivo corriente', 'naturaleza_id' => 3],
            ['codigo' => 2101, 'nombre' => 'SOBRE GIROS BANCARIOS', 'descripcion' => 'Pasivo por sobregiros bancarios', 'naturaleza_id' => 3],
            ['codigo' => 2102, 'nombre' => 'CUENTAS COMERCIALES POR PAGAR', 'descripcion' => 'Obligaciones comerciales a corto plazo', 'naturaleza_id' => 3],
            ['codigo' => 2103, 'nombre' => 'DOCUMENTOS POR COBRAR DESCONTADOS', 'descripcion' => 'Documentos descontados por cobrar', 'naturaleza_id' => 3],
            ['codigo' => 2104, 'nombre' => 'CUENTAS Y DOCUMENTOS POR PAGAR', 'descripcion' => 'Obligaciones documentadas por pagar', 'naturaleza_id' => 3],
            ['codigo' => 2105, 'nombre' => 'PRESTAMOS POR PAGAR', 'descripcion' => 'Préstamos a corto plazo', 'naturaleza_id' => 3],
            ['codigo' => 2106, 'nombre' => 'RETENCIONES POR PAGAR', 'descripcion' => 'Retenciones pendientes de pago', 'naturaleza_id' => 3],
            ['codigo' => 2107, 'nombre' => 'OBLIGACIONES POR BENEFICIOS A EMPLEADOS A CORTO PLAZO', 'descripcion' => 'Beneficios a empleados pendientes', 'naturaleza_id' => 3],
            ['codigo' => 2108, 'nombre' => 'DIVIDENDOS POR PAGAR', 'descripcion' => 'Dividendo pendiente de pago', 'naturaleza_id' => 3],
            ['codigo' => 2109, 'nombre' => 'IVA DEBITO FISCAL', 'descripcion' => 'Débito fiscal por IVA', 'naturaleza_id' => 3],
            ['codigo' => 2110, 'nombre' => 'IVA PERCIBIDO Y RETENIDO POR PAGAR', 'descripcion' => 'IVA retenido pendiente de pago', 'naturaleza_id' => 3],
            ['codigo' => 2111, 'nombre' => 'IMPUESTOS POR PAGAR', 'descripcion' => 'Obligaciones tributarias pendientes', 'naturaleza_id' => 3],
            ['codigo' => 2113, 'nombre' => 'INTERESES POR PAGAR', 'descripcion' => 'Intereses pendientes de pago', 'naturaleza_id' => 3],
            ['codigo' => 22, 'nombre' => 'NO CORRIENTE', 'descripcion' => 'Pasivo no corriente', 'naturaleza_id' => 3],
            ['codigo' => 2201, 'nombre' => 'PRESTAMOS POR PAGAR', 'descripcion' => 'Préstamos a largo plazo', 'naturaleza_id' => 3],
            ['codigo' => 2202, 'nombre' => 'INGRESOS DIFERIDOS', 'descripcion' => 'Ingresos diferidos a largo plazo', 'naturaleza_id' => 3],
            ['codigo' => 2203, 'nombre' => 'PROVISION PARA OBLIGACIONES LABORALES', 'descripcion' => 'Provisión para obligaciones laborales', 'naturaleza_id' => 3],
            ['codigo' => 2204, 'nombre' => 'PASIVO POR IMPUESTO SOBRE LA RENTA DIFERIDO', 'descripcion' => 'Impuesto diferido sobre la renta', 'naturaleza_id' => 3],

            // Cuentas de Patrimonio
            ['codigo' => 3, 'nombre' => 'PATRIMONIO', 'descripcion' => 'Cuenta de patrimonio', 'naturaleza_id' => 3],
            ['codigo' => 31, 'nombre' => 'CAPITAL CONTABLE', 'descripcion' => 'Capital contable de la empresa', 'naturaleza_id' => 3],
            ['codigo' => 3101, 'nombre' => 'CAPITAL SOCIAL', 'descripcion' => 'Capital social de la empresa', 'naturaleza_id' => 3],
            ['codigo' => 3102, 'nombre' => 'R CAPITAL SUSCRITO NO PAGADO', 'descripcion' => 'Capital suscrito pendiente de pago', 'naturaleza_id' => 3],
            ['codigo' => 3103, 'nombre' => 'RESERVA LEGAL', 'descripcion' => 'Reserva legal de la empresa', 'naturaleza_id' => 3],
            ['codigo' => 3104, 'nombre' => 'RESERVAS VOLUNTARIAS', 'descripcion' => 'Reservas voluntarias', 'naturaleza_id' => 3],
            ['codigo' => 3105, 'nombre' => 'UTILIDADES RETENIDAS', 'descripcion' => 'Utilidades retenidas de ejercicios anteriores', 'naturaleza_id' => 3],
            ['codigo' => 3106, 'nombre' => 'UTILIDAD DEL EJERCICIO', 'descripcion' => 'Utilidad generada en el ejercicio actual', 'naturaleza_id' => 3],
            ['codigo' => 3107, 'nombre' => 'R DÉFICIT', 'descripcion' => 'Déficit acumulado', 'naturaleza_id' => 3],
        
            // Cuentas de Resultados Deudoras
            ['codigo' => 4, 'nombre' => 'CUENTAS DE RESULTADOS DEUDORAS', 'descripcion' => 'Cuenta de resultados deudora', 'naturaleza_id' => 1],
            ['codigo' => 41, 'nombre' => 'COSTOS', 'descripcion' => 'Costos de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 4101, 'nombre' => 'COSTOS DE VENTA', 'descripcion' => 'Costos asociados a las ventas', 'naturaleza_id' => 1],
            ['codigo' => 42, 'nombre' => 'GASTOS', 'descripcion' => 'Gastos generales de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 4201, 'nombre' => 'GASTOS DE ADMINISTRACIÓN', 'descripcion' => 'Gastos administrativos de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 4202, 'nombre' => 'GASTOS DE VENTAS', 'descripcion' => 'Gastos asociados a la venta de productos o servicios', 'naturaleza_id' => 1],
            ['codigo' => 4203, 'nombre' => 'COSTOS FINANCIEROS', 'descripcion' => 'Costos financieros de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 4204, 'nombre' => 'GASTOS POR IMPUESTO SOBRE LA RENTA CORRIENTE', 'descripcion' => 'Gastos por impuesto corriente sobre la renta', 'naturaleza_id' => 1],
            ['codigo' => 4205, 'nombre' => 'GASTOS POR IMPUESTO SOBRE LA RENTA DIFERIDO', 'descripcion' => 'Gastos por impuesto diferido sobre la renta', 'naturaleza_id' => 1],
            ['codigo' => 43, 'nombre' => 'PERDIDAS', 'descripcion' => 'Pérdidas de la empresa', 'naturaleza_id' => 1],
            ['codigo' => 4301, 'nombre' => 'PERDIDAS EN VENTA DE ACTIVO DE EXPLOTACIÓN', 'descripcion' => 'Pérdidas por venta de activos de explotación', 'naturaleza_id' => 1],
            ['codigo' => 4302, 'nombre' => 'PERDIDAS POR DETERIORO DE ACTIVOS', 'descripcion' => 'Pérdidas por deterioro en el valor de activos', 'naturaleza_id' => 1],
            ['codigo' => 4303, 'nombre' => 'PERDIDAS POR CASOS FORTUITOS EN ACTIVOS', 'descripcion' => 'Pérdidas fortuitas en activos', 'naturaleza_id' => 1],
            ['codigo' => 4304, 'nombre' => 'DETERIORO Y PERDIDAS DE INSTRUMENTOS FINANCIEROS', 'descripcion' => 'Deterioro y pérdidas en instrumentos financieros', 'naturaleza_id' => 1],
            ['codigo' => 44, 'nombre' => 'COMPRAS', 'descripcion' => 'Compras realizadas por la empresa', 'naturaleza_id' => 1],
            ['codigo' => 45, 'nombre' => 'REBAJAS Y DEVOLUCIONES SOBRE VENTAS', 'descripcion' => 'Rebajas y devoluciones aplicadas sobre ventas', 'naturaleza_id' => 1],
            ['codigo' => 46, 'nombre' => 'GASTOS DE COMPRA', 'descripcion' => 'Gastos asociados a las compras', 'naturaleza_id' => 1],
            ['codigo' => 4601, 'nombre' => 'TRANSPORTE DE MERCANCIA', 'descripcion' => 'Gastos de transporte de mercancías', 'naturaleza_id' => 1],
        
            // Cuentas de Resultados Acreedoras
            ['codigo' => 5, 'nombre' => 'CUENTAS DE RESULTADOS ACREEDORAS', 'descripcion' => 'Cuenta de resultados acreedora', 'naturaleza_id' => 3],
            ['codigo' => 51, 'nombre' => 'INGRESOS DE OPERACIÓN', 'descripcion' => 'Ingresos por operaciones de la empresa', 'naturaleza_id' => 3],
            ['codigo' => 5101, 'nombre' => 'VENTAS', 'descripcion' => 'Ingresos por ventas de bienes o servicios', 'naturaleza_id' => 3],
            ['codigo' => 5102, 'nombre' => 'INGRESOS POR IMPUESTO SOBRE LA RENTA DIFERIDO', 'descripcion' => 'Ingresos diferidos por impuesto sobre la renta', 'naturaleza_id' => 3],
            ['codigo' => 52, 'nombre' => 'GANANCIAS', 'descripcion' => 'Ganancias de la empresa', 'naturaleza_id' => 3],
            ['codigo' => 5201, 'nombre' => 'GANANCIAS POR INTERESES', 'descripcion' => 'Ingresos por intereses ganados', 'naturaleza_id' => 3],
            ['codigo' => 5202, 'nombre' => 'GANANCIAS EN VENTA DE ACTIVO DE EXPLOTACIÓN', 'descripcion' => 'Ganancias por venta de activos de explotación', 'naturaleza_id' => 3],
            ['codigo' => 5203, 'nombre' => 'OTRAS GANANCIAS', 'descripcion' => 'Otras ganancias obtenidas', 'naturaleza_id' => 3],
            ['codigo' => 5204, 'nombre' => 'REVERSION DE DETERIORO Y GANANCIAS EN VENTA DE INSTRUMENTOS FINANCIEROS', 'descripcion' => 'Ganancias por reversión de deterioro en instrumentos financieros', 'naturaleza_id' => 3],
            ['codigo' => 53, 'nombre' => 'REBAJAS Y DEVOLUCIONES SOBRE COMPRAS', 'descripcion' => 'Rebajas y devoluciones aplicadas sobre compras', 'naturaleza_id' => 3],
        
            // Cuenta de Cierre
            ['codigo' => 6, 'nombre' => 'CUENTA DE CIERRE', 'descripcion' => 'Cuenta utilizada para el cierre del ejercicio', 'naturaleza_id' => 3],
            ['codigo' => 61, 'nombre' => 'CUENTA LIQUIDADORA', 'descripcion' => 'Cuenta que liquida las operaciones del ejercicio', 'naturaleza_id' => 3],
            ['codigo' => 6101, 'nombre' => 'PÉRDIDAS Y GANANCIAS', 'descripcion' => 'Cuenta de pérdidas y ganancias para el cierre del ejercicio', 'naturaleza_id' => 3],
            ['codigo' => 6102, 'nombre' => 'RESERVA LEGAL', 'descripcion' => 'Reserva legal del ciclo contable', 'naturaleza_id' => 3], 
            ['codigo' => 6103, 'nombre' => 'INVENTARIO FINAL', 'descripcion' => 'Inventario final del ciclo contable', 'naturaleza_id' => 1], 
            
        ]);
    }
}
