using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace wsreporteria.Entity
{
    public class EListarFillRateXsku
    {
        public String ALMACEN { get; set; }
        public String PERIODO { get; set; }
        public String SKU { get; set; }
        public String NOMBRE_PRODUCTO { get; set; }
        public String FAMILIA { get; set; }
        public Int32 CANTIDAD_ORDEN { get; set; }
        public Int32 CANTIDAD_LLEVA { get; set; }
        public Double PORCENTAJE { get; set; }
        public Int32 ROW { get; set; }
        public String GRUPO { get; set; }
        public String COMENTARIO { get; set; }
    }
}