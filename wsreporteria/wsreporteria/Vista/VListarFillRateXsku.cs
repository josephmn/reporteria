using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarFillRateXsku : BDconexion
    {
        public List<EListarFillRateXsku> ListarFillRateXsku(Int32 post, String almacen)
        {
            List<EListarFillRateXsku> lCListarFillRateXsku = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarFillRateXsku oVListarFillRateXsku = new CListarFillRateXsku();
                    lCListarFillRateXsku = oVListarFillRateXsku.ListarFillRateXsku(con, post, almacen);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarFillRateXsku);
        }
    }
}