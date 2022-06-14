using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarEmbarquesXsku : BDconexion
    {
        public List<EListarEmbarquesXsku> ListarEmbarquesXsku(String orden, String embarque, String sku)
        {
            List<EListarEmbarquesXsku> lCListarEmbarquesXsku = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarEmbarquesXsku oVListarEmbarquesXsku = new CListarEmbarquesXsku();
                    lCListarEmbarquesXsku = oVListarEmbarquesXsku.ListarEmbarquesXsku(con, orden, embarque, sku);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarEmbarquesXsku);
        }
    }
}