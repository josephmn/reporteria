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
    public class CListarFVDXsku
    {
        public List<EListarFVDXsku> ListarFVDXsku(SqlConnection con, Int32 post, String almacen)
        {
            List<EListarFVDXsku> lEListarFVDXsku = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_FVD_SKU", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@post", SqlDbType.Int).Value = post;
            cmd.Parameters.AddWithValue("@almacen", SqlDbType.VarChar).Value = almacen;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarFVDXsku = new List<EListarFVDXsku>();

                EListarFVDXsku obEListarFVDXsku = null;
                while (drd.Read())
                {
                    obEListarFVDXsku = new EListarFVDXsku();
                    obEListarFVDXsku.ROW = Convert.ToInt32(drd["ROW"].ToString());
                    obEListarFVDXsku.ALMACEN = drd["ALMACEN"].ToString();
                    obEListarFVDXsku.SKU = drd["SKU"].ToString();
                    obEListarFVDXsku.DESCRIPCION = drd["DESCRIPCION"].ToString();
                    obEListarFVDXsku.CANTIDAD_TOTAL = Convert.ToDouble(drd["CANTIDAD_TOTAL"].ToString());
                    obEListarFVDXsku.MONTO = Convert.ToDouble(drd["MONTO"].ToString());
                    obEListarFVDXsku.FAMILIA = drd["FAMILIA"].ToString();
                    obEListarFVDXsku.FECHA = drd["FECHA"].ToString();
                    obEListarFVDXsku.PERIODO = drd["PERIODO"].ToString();
                    obEListarFVDXsku.VEN_QTY_PROCESADA = Convert.ToDouble(drd["VEN_QTY_PROCESADA"].ToString());
                    obEListarFVDXsku.VEN_TOT_PROCESADA = Convert.ToDouble(drd["VEN_TOT_PROCESADA"].ToString());
                    obEListarFVDXsku.VEN_QTY_CANCELADA = Convert.ToDouble(drd["VEN_QTY_CANCELADA"].ToString());
                    obEListarFVDXsku.VEN_TOT_CANCELADA = Convert.ToDouble(drd["VEN_TOT_CANCELADA"].ToString());
                    obEListarFVDXsku.GRUPO = drd["GRUPO"].ToString();
                    obEListarFVDXsku.COMENTARIO = drd["COMENTARIO"].ToString();
                    lEListarFVDXsku.Add(obEListarFVDXsku);
                }
                drd.Close();
            }

            return (lEListarFVDXsku);
        }
    }
}