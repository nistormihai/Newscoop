/******************************************************************************

CAMPSITE is a Unicode-enabled multilingual web content
management system for news publications.
CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
Copyright (C)2000,2001  Media Development Loan Fund
contact: contact@campware.org - http://www.campware.org
Campware encourages further development. Please let us know.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

******************************************************************************/

#include <sys/time.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include <iostream>
#include <sstream>

#include "globals.h"
#include "csocket.h"
#include "readconf.h"
#include "cxmltree.h"
#include "cgi.h"
#include "cgi_common.h"


#define RECV_BUF_LEN	(1000)


using std::cout;
using std::endl;
using std::stringstream;
using std::ios_base;


void ReadConf(string& p_rcoIP, int& p_rnPort);
int ReadParameters(char** p_ppchMsg, int* p_pnSize, const char** p_ppchErrMsg);


int main()
{
	cout << "Content-type: text/html; charset=UTF-8\n\n";
	string coIP;
	int nPort;
	ReadConf(coIP, nPort);
	int nErrNo;
	int nSize;
	char* pchMsg;
	const char* pchErrMsg;
	if ((nErrNo = ReadParameters(&pchMsg, &nSize, &pchErrMsg)) != 0)
	{
		if (pchErrMsg == 0)
			pchErrMsg = "Error reading parameters";
		cout << "<html>\n<head>\n<title>REQUEST ERROR</title>\n</head>\n"
			 << "<body>\n" << pchErrMsg << "\n</body>\n</html>\n";
		return 0;
	}
	stringstream coMsg;
	coMsg << "   1 ";
	coMsg.width(4);
	coMsg.fill(' ');
	coMsg.setf(ios_base::hex, ios_base::basefield);
	coMsg << nSize;
	coMsg << " " << pchMsg;
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = 60;
	fd_set clSet;
	FD_ZERO(&clSet);
	CTCPSocket coSock;
	try
	{
		coSock.Connect(coIP.c_str(), nPort);
		coSock.Send(coMsg.str().c_str(), nSize+10);
		FD_SET((SOCKET)coSock, &clSet);
		for (;;)
		{
			if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
				|| !FD_ISSET((SOCKET)*coSock, &clSet))
			{
				throw ConfException("Error on select");
			}
			char pchBuff[RECV_BUF_LEN + 1];	/* +1 for null char */
			int nReceived = coSock.Recv(pchBuff, RECV_BUF_LEN);
			if (nReceived == -1)
				throw ConfException("Error receiving packet");
			if (nReceived == 0)
				break;
			pchBuff[nReceived] = 0;
			cout << pchBuff;
		}
		coSock.Shutdown();
	}
	catch (ConfException& rcoEx)
	{
		cout << "<html>\n" << rcoEx.what() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	catch (ConnectRefused& rcoEx)
	{
		cout << "<html>\n" << rcoEx.Message() << " " << rcoEx.Host() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	catch (SocketErrorException& rcoEx)
	{
		cout << "<html>\n" << rcoEx.Message() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	return 0;
}


void ReadConf(string& p_rcoIP, int& p_rnPort)
{
	char* pchDocumentRoot = getenv("DOCUMENT_ROOT");
	try
	{
		// the parser IP address is always localhost
		p_rcoIP = "127.0.0.1";

		// read parser configuration
		string coParserConfFile = string(pchDocumentRoot) + "/parser_conf.php";
		ConfAttrValue m_coAttributes(coParserConfFile);
		p_rnPort = atoi(m_coAttributes.valueOf("PARSER_PORT").c_str());
	}
	catch (ConfException& rcoEx)
	{
		cout << "Error reading configuration: " << rcoEx.what() << endl;
		exit(0);
	}
}


int ReadParameters(char** p_ppchMsg, int* p_pnSize, const char** p_ppchErrMsg)
{
	char* pchHTTPHost = 0;
	char* pchDocumentRoot = 0;
	char* pchIP = 0;
	char* pchPathTranslated = 0;
	char* pchRequestMethod = 0;
	char* pchRequestURI = 0;
	char* pchQueryString = 0;
	char* pchHttpCookie = 0;
	try
	{
		char* pchTmp;
		if ((pchTmp = getenv("HTTP_HOST")) == NULL)
		{
			throw ExReadParams(-1,"Can not get HTTP HOST");
		}
		pchHTTPHost = strdup(pchTmp);
		if ((pchTmp = getenv("DOCUMENT_ROOT")) == NULL)
		{
			throw ExReadParams(-1,"Can not get DOCUMENT ROOT");
		}
		pchDocumentRoot = strdup(pchTmp);
		if ((pchTmp = getenv("REMOTE_ADDR")) == NULL)
		{
			throw ExReadParams(-2, "Can not get REMOTE_ADDR");
		}
		pchIP = strdup(pchTmp);
		if ((pchTmp = getenv("PATH_TRANSLATED")) == NULL)
		{
			throw ExReadParams(-3, "Can not translate path");
		}
		pchPathTranslated = strdup(pchTmp);
		if (strcmp(pchTmp, "/dev/stdin") == 0 || strcmp(pchTmp, "-") == 0)
		{
			throw ExReadParams(-4, "Unable to parse from stdin");
		}
		if ((pchTmp = getenv("REQUEST_METHOD")) == NULL)
		{
			throw ExReadParams(-7, "Can not get REQUEST_METHOD");
		}
		pchRequestMethod = strdup(pchTmp);
		if ((pchTmp = getenv("REQUEST_URI")) == NULL)
		{
			throw ExReadParams(-7, "Can not get REQUEST_URI");
		}
		pchRequestURI = strdup(pchTmp);
		if (strcmp(pchRequestMethod, "GET") == 0)
		{
			if ((pchTmp = getenv("QUERY_STRING")) == NULL)
			{
				throw ExReadParams(-8, "Can not get QUERY_STRING");
			}
			pchQueryString = strdup(pchTmp);
		}
		else if (strcmp(pchRequestMethod, "POST") == 0)
		{
			pchQueryString = ReadPOSTQuery();
			if (pchQueryString == NULL)
			{
				throw ExReadParams(-8, "Can not get QUERY_STRING");
			}
		}
		pchTmp = getenv("HTTP_COOKIE");
		pchHttpCookie = strdup(pchTmp != NULL ? pchTmp : "");
	}
	catch (ExReadParams& rcoEx)
	{
		if (pchHTTPHost != NULL)
			free(pchHTTPHost);
		if (pchDocumentRoot != NULL)
			free(pchDocumentRoot);
		if (pchIP != NULL)
			free(pchIP);
		if (pchPathTranslated != NULL)
			free(pchPathTranslated);
		if (pchRequestMethod != NULL)
			free(pchRequestMethod);
		if (pchRequestURI != NULL)
			free(pchRequestURI);
		if (pchQueryString != NULL)
			free(pchQueryString);
		if (pchHttpCookie != NULL)
			free(pchHttpCookie);
		*p_ppchMsg = NULL;
		*p_ppchErrMsg = rcoEx.ErrMsg();
		int nErrNo = rcoEx.ErrNo();
		return nErrNo;
	}
	CXMLTree coTree("CampsiteMessage");
	CXMLTree::iterator coRootIt = coTree.getRootNode();
	coTree.addAttribute(coRootIt, "MessageType", "URLRequest");
	coTree.newChild(coRootIt, "HTTPHost", pchHTTPHost);
	coTree.newChild(coRootIt, "DocumentRoot", pchDocumentRoot);
	coTree.newChild(coRootIt, "RemoteAddress", pchIP);
	coTree.newChild(coRootIt, "PathTranslated", pchPathTranslated);
	coTree.newChild(coRootIt, "RequestMethod", pchRequestMethod);
	coTree.newChild(coRootIt, "RequestURI", pchRequestURI);
	CXMLTree::iterator coNodeIt;
	coNodeIt = coTree.newChild(coRootIt, "Parameters");
	string::size_type nStart = 0;
	string coQueryString = pchQueryString;
	CGI coCgi(pchRequestMethod, pchQueryString);
	coCgi.ResetIterator();
	const char* pchParam;
	const char* pchValue;
	while (coCgi.GetNextParameter(&pchParam, &pchValue))
	{
		CXMLTree::iterator coParamIt = coTree.newChild(coNodeIt, "Parameter", pchValue);
		coTree.addAttribute(coParamIt, "Name", pchParam);
		coTree.addAttribute(coParamIt, "Type", "string");
	}
	string coCookies = pchHttpCookie;
	coNodeIt = coTree.newChild(coRootIt, "Cookies");
	nStart = 0;
	while (true)
	{
		// read the parameter name
		string::size_type nIndex = coCookies.find('=', nStart);
		if (nIndex == string::npos)
			break;
		string coCookie = coCookies.substr(nStart, nIndex - nStart);

		// read the parameter value
		nStart = nIndex + 1;
		nIndex = coCookies.find(";", nStart);
		nIndex = nIndex == string::npos ? coCookies.size() : nIndex;
		string coValue = coCookies.substr(nStart, nIndex - nStart);
		trim(coCookie);
		trim(coValue);
		if (coValue == "")
			continue;

		CXMLTree::iterator coParamIt = coTree.newChild(coNodeIt, "Cookie", coValue.c_str());
		coTree.addAttribute(coParamIt, "Name", coCookie.c_str());

		// prepare for the next iteration
		nStart = nIndex + 1;
		if (nStart >= coCookies.size())
			break;
	}
	
	coTree.saveToMemory(p_ppchMsg, p_pnSize);

	return 0;
}
