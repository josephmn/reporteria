using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace wsreporteria.Entity
{
    public class EListarOrdenVentaDet
    {
        public String ORDEN_VENTA { get; set; }
        public DateTime FECHA_ORDEN { get; set; }
        public String ALMACEN { get; set; }
        public String RUC { get; set; }
        public String RAZON { get; set; }
        public Int32 FILA { get; set; }
        public String SKU { get; set; }
        public String NOMBRE_PRODUCTO { get; set; }
        public Double PRECIO { get; set; }
        public Double CANTIDAD_ORDEN { get; set; }
        public Double MONTO_TOTAL { get; set; }
        public Double MONTO_RESTANTE { get; set; }
    }
}