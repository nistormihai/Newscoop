/*
 * @(#)CampSystem.java
 *
 * Copyright (c) 2000,2001 Media Development Loan Fund
 *
 * CAMPSITE is a Unicode-enabled multilingual web content                     
 * management system for news publications.                                   
 * CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.         
 * Copyright (C)2000,2001  Media Development Loan Fund                        
 * contact: contact@campware.org - http://www.campware.org                    
 * Campware encourages further development. Please let us know.               
 *                                                                            
 * This program is free software; you can redistribute it and/or              
 * modify it under the terms of the GNU General Public License                
 * as published by the Free Software Foundation; either version 2             
 * of the License, or (at your option) any later version.                     
 *                                                                            
 * This program is distributed in the hope that it will be useful,            
 * but WITHOUT ANY WARRANTY; without even the implied warranty of             
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               
 * GNU General Public License for more details.                               
 *                                                                            
 * You should have received a copy of the GNU General Public License          
 * along with this program; if not, write to the Free Software                
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


    /**
     * CampSystem is a system object which contains some
     * methods common for most of objects
     */
import javax.swing.*;
     
public final class CampSystem
{
	public static void showInfo(String s)
	{
       //JOptionPane op=new JOptionPane();
       //op.showMessageDialog(null,s,"Info",JOptionPane.INFORMATION_MESSAGE);
	}

	public static void showError(String s)
	{
        //JOptionPane op=new JOptionPane();
        //op.showMessageDialog(null,s,"Stop",JOptionPane.ERROR_MESSAGE);
	}

	public static void showStatus(String s)
	{
        //status.setText(s);
        //status.revalidate();
	}
}
