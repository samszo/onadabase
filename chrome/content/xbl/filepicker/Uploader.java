/*
 * (c) Edutice 2007 http://www.edutice.fr
 * Author: vbe <v.bataille@novatice.com>
 * Version: see changeLog
 * Note: Edutice is a brand of Novatice Technologies SAS
 */


package com.novatice.rss.servlets;

import com.novatice.rss.RssManager;
import java.io.*;
import java.net.*;
import java.util.Iterator;
import java.util.List;

import javax.servlet.*;
import javax.servlet.http.*;
import org.apache.commons.fileupload.FileItem;
import org.apache.commons.fileupload.FileItemFactory;
import org.apache.commons.fileupload.disk.DiskFileItemFactory;
import org.apache.commons.fileupload.servlet.ServletFileUpload;
import org.apache.log4j.Logger;


public class Uploader extends HttpServlet {
    
    static Logger logger = Logger.getLogger(Uploader.class);
    
    /** Processes requests for both HTTP <code>GET</code> and <code>POST</code> methods.
     * @param request servlet request
     * @param response servlet response
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        
        RssManager manager = RssManager.getManager(request, getServletContext().getRealPath(""));
        String pageUrl = request.getHeader("Referer");
        String uploadDir = "";
        String resParam = "";
        String filesName = "";
        
        String BASE_DIRECTORY = manager.getItemsPath();
        boolean isMultipart = ServletFileUpload.isMultipartContent(request);
        
        // check if the http request is a multipart request
        // with other words check that the http request can have uploaded files
        if (isMultipart) {
            
            //  Create a factory for disk-based file items
            FileItemFactory factory = new DiskFileItemFactory();
            
            //  Create a new file upload handler
            ServletFileUpload servletFileUpload = new ServletFileUpload(factory);
            servletFileUpload.setSizeMax(-1);
            
            try {
                // Parse the request
                List items = servletFileUpload.parseRequest(request);
                
                // Process the uploaded items
                Iterator iter = items.iterator();
                
                while (iter.hasNext()) {
                    FileItem item = (FileItem) iter.next();
                    
                    //deal with no file type parameters
                    if (item.isFormField()) {
                        
                        String name = item.getFieldName();
                        if (name.equals("uploadDir")) {
                            uploadDir = item.getString();
                        }
                        
                        // retrieve the files
                    } else {
                        // the fileNames are urlencoded
                        String fileName = URLDecoder.decode(item.getName(), "UTF-8");
                        filesName += (filesName.equals("")) ? fileName : ";"+fileName;
                        
                        File file = new File("", fileName);
                        file = new File(BASE_DIRECTORY+"/"+uploadDir, file.getPath());
                        
                        // retrieve the parent file for creating the directories
                        File parentFile = file.getParentFile();
                        
                        if (parentFile != null) {
                            parentFile.mkdirs();
                        }
                        
                        // writes the file to the filesystem
                        item.write(file);
                    }
                }
                
                resParam += "status=ok&dir="+BASE_DIRECTORY+"&files="+filesName;
                response.setStatus(HttpServletResponse.SC_OK);
                
            } catch (Exception e) {
                logger.error(e.getMessage());
                resParam = "status=inter";
                response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            }
            
        } else {
            logger.error("bad request");
            resParam = "status=bad";
            response.setStatus(HttpServletResponse.SC_BAD_REQUEST);
        }
        
        pageUrl += (pageUrl.contains("?")) ? "&" : "?";
        pageUrl += resParam;
        response.sendRedirect(pageUrl);
    }
    
    
// <editor-fold defaultstate="collapsed" desc="HttpServlet methods. Click on the + sign on the left to edit the code.">
    /** Handles the HTTP <code>GET</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        processRequest(request, response);
    }
    
    /** Handles the HTTP <code>POST</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        processRequest(request, response);
    }
    
    /** Returns a short description of the servlet.
     */
    public String getServletInfo() {
        return "Short description";
    }
// </editor-fold>
}
