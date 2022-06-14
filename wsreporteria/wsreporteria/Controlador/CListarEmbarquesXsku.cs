using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Collections.Specialized;
using System.Linq;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using wsreporteria.Entity;

namespace wsreporteria.Controller
{
    public class CListarEmbarquesXsku
    {
        public List<EListarEmbarquesXsku> ListarEmbarquesXsku(SqlConnection con, String orden, String embarque, String sku)
        {
            List<EListarEmbarquesXsku> lEListarEmbarquesXsku = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_EMBARQUES_SKU", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@orden", SqlDbType.VarChar).Value = orden;
            cmd.Parameters.AddWithValue("@embarque", SqlDbType.VarChar).Value = embarque;
            cmd.Parameters.AddWithValue("@sku", SqlDbType.VarChar).Value = sku;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarEmbarquesXsku = new List<EListarEmbarquesXsku>();

                EListarEmbarquesXsku obEListarEmbarquesXsku = null;
                while (drd.Read())
                {
                    obEListarEmbarquesXsku = new EListarEmbarquesXsku();
                    obEListarEmbarquesXsku.ORDEN_VENTA = drd["ORDEN_VENTA"].ToString();
                    obEListarEmbarquesXsku.FILA = Convert.ToInt32(drd["FILA"].ToString());
                    obEListarEmbarquesXsku.SKU = drd["SKU"].ToString();
                    obEListarEmbarquesXsku.PRECIO = Convert.ToDouble(drd["PRECIO"].ToString());
                    obEListarEmbarquesXsku.CANTIDAD_ORDEN = Convert.ToDouble(drd["CANTIDAD_ORDEN"].ToString());
                    obEListarEmbarquesXsku.CANTIDAD_LLEVA = Convert.ToDouble(drd["CANTIDAD_LLEVA"].ToString());
                    lEListarEmbarquesXsku.Add(obEListarEmbarquesXsku);
                }
                drd.Close();
            }

            return (lEListarEmbarquesXsku);
        }
    }
}