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

/******************************************************************************

Implementation of the classes defined in threadpool.h

******************************************************************************/

#include <signal.h>
#include <unistd.h>
#include <iostream>

#include "threadpool.h"

using std::cout;
using std::endl;

typedef struct ThreadLocal
{
	CThreadPool* m_pcoThreadPool;
	UInt m_nThreadNr;

	ThreadLocal(CThreadPool* p_pcoThreadPool, UInt p_nThreadNr)
		: m_pcoThreadPool(p_pcoThreadPool), m_nThreadNr(p_nThreadNr) {}
} ThreadLocal;

// Constructor
// Parameters:
//		UInt p_nMinThr - the number of threads to create in advance
//		UInt p_nMaxThr - number of maximum threads to create
//		void* (*p_pStartRoutine)(void *) - pointer to thread start routine
//		void* p_pArg - pointer to parameter to pass to thread start routine
// Throws ExThread exception on error
CThreadPool::CThreadPool(UInt p_nMinThr, UInt p_nMaxThr, void* (*p_pStartRoutine)(void *),
                         void* p_pArg) throw(ExThread)
	: m_nMinThreads(p_nMinThr), m_nMaxThreads(p_nMaxThr), m_pStartRoutine(p_pStartRoutine), m_pArg(p_pArg)
{
	if (m_nMinThreads > m_nMaxThreads)
		m_nMaxThreads = m_nMinThreads;
	m_nCreatedThreads = m_nWorkingThreads = 0;
	m_pThreads = 0;
	if (m_nMinThreads == 0)
		return ;
	try
	{
		m_pThreads = (ThreadInfo*) new ThreadInfo[m_nMaxThreads];
		if (m_pThreads == 0)
			throw ExThread(ThreadSvAbort, "Unable to initialize thread array.");
		LockMutex();
		for (UInt nIndex = 0; nIndex < m_nMaxThreads; nIndex++)
		{
			m_pThreads[nIndex].m_bCreated = false;
			m_pThreads[nIndex].m_bWorking = false;
			m_pThreads[nIndex].m_pArg = m_pArg;
			sem_init(&(m_pThreads[nIndex].m_Start), 0, 0);
			if (m_nCreatedThreads < m_nMinThreads)
				CreateThread(nIndex);
		}
		UnlockMutex();
	}
	catch (ExThread& coEx)
	{
		if (coEx.Severity() == ThreadSvAbort)
		{
			if (m_pThreads != 0)
				delete m_pThreads;
			m_pThreads = 0;
			delete this;
		}
		throw coEx;
	}
}

CThreadPool::~CThreadPool()
{
	try
	{
		for (UInt nIndex = 0; nIndex < m_nMaxThreads; nIndex++)
			if (m_pThreads[nIndex].m_bCreated)
				pthread_kill(m_pThreads[nIndex].m_nThread, SIGTERM);
		for (; m_nCreatedThreads > 0;)
			usleep(1000);
		LockMutex();
		for (UInt nIndex = 0; nIndex < m_nMaxThreads; nIndex++)
			sem_destroy(&(m_pThreads[nIndex].m_Start));
		if (m_pThreads != 0)
			delete m_pThreads;
		m_pThreads = 0;
		UnlockMutex();
	}
	catch (ExThread& coEx)
	{
		UnlockMutex();
	}
}

// startThread: Start a thread
// Parameters:
//		bool p_bUserDefArg - if true will supply the second parameter to thread routine;
//			otherwise will supply p_pArg parameter from constructor to the thread routine
//		void* p_pArg - parameter to supply to thread routine
// Throws:
//		ExThreadNotFree - all threads are occupied
//		ExThreadErrCreate - cannot create a new thread
void CThreadPool::startThread(bool p_bUserDefArg, void* p_pArg)
throw(ExThreadNotFree, ExThreadErrCreate)
{
	CMutexHandler coMh(&m_coMutex);
	if (m_nWorkingThreads >= m_nMaxThreads)		// is there any free (not working) thread?
		throw ExThreadNotFree();
	UInt nIndex = 0;
	bool bCreate = m_nWorkingThreads >= m_nCreatedThreads;
	for (nIndex = 0; nIndex < m_nMaxThreads; nIndex++)	// search the pool for a free thread
	{
		if ((bCreate && !m_pThreads[nIndex].m_bCreated)
		        || (!bCreate && m_pThreads[nIndex].m_bCreated && !m_pThreads[nIndex].m_bWorking))
		{		// found a free thread (created but not working or not created)
			m_pThreads[nIndex].m_pArg = p_bUserDefArg ? p_pArg : m_pArg;
			if (bCreate)
				CreateThread(nIndex);
			m_pThreads[nIndex].m_bWorking = true;
			m_nWorkingThreads++;
			break;
		}
	}
	if (nIndex >= m_nMaxThreads)
		throw ExThreadNotFree();
	Debug("StartThread: starting", false, 0, true, nIndex);
	sem_post(&(m_pThreads[nIndex].m_Start));	// let the thread start
}

// waitFreeThread: returns when there is at least one free thread
// Parameters:
//		ULInt p_nUSec - time out (microseconds); 0 if wait forever
// Returns true if at least one thread is free, false otherwise
bool CThreadPool::waitFreeThread(ULInt p_nUSec) const
{
	LockMutex();
	bool bIsFree = m_nWorkingThreads < m_nMaxThreads;
	UnlockMutex();
	if (bIsFree)
	{
		return true;
	}
	ULInt nSleepTime = p_nUSec != 0 ? p_nUSec : 100000;
	bool bLoop = p_nUSec == 0;
	if (bLoop)
		cout << "loop" << endl;
	while (bLoop)
	{
		usleep(nSleepTime);
		LockMutex();
		if ((bIsFree = m_nWorkingThreads < m_nMaxThreads) == true)
			break;
		cout << "working: " << m_nWorkingThreads << ", max: " << m_nMaxThreads << endl;
		UnlockMutex();
	}
	UnlockMutex();
	return bIsFree;
}

inline void CThreadPool::Debug(const char* p_pchArg1, bool p_bArg, const void* p_pchArg2, bool p_bIndex,
                               UInt p_nIndex)
{
#ifdef _DEBUG
	cout << '[' << pthread_self() << ", ";
	if (p_bIndex)
		cout << p_nIndex;
	else
		cout << '-';
	cout << ", c=" << m_nCreatedThreads << ", w=" << m_nWorkingThreads << "]: "
	     << (p_pchArg1 != 0 ? p_pchArg1 : "");
	if (p_bArg)
		cout << (ULInt)p_pchArg2;
	cout << endl;
#endif
}

inline void CThreadPool::LockMutex() const throw (ExThread)
{
	try
	{
		m_coMutex.lock();
	}
	catch (ExMutex& rcoEx)
	{
		throw ExThread(rcoEx.Severity(), rcoEx.Message());
	}
}

inline void CThreadPool::UnlockMutex() const throw (ExThread)
{
	m_coMutex.unlock();
}

// ThreadRoutine: this is the routine supplied for the newly created thread
// Parameters: void* p_pThreadLocal - pointer to a ThreadLocal structure; this is passed by
//		createThread method to threadRoutine
// Returns: pointer to void (always NULL)
void* CThreadPool::ThreadRoutine(void* p_pThreadLocal)
{
	ThreadLocal* pThreadLocal = (ThreadLocal*)p_pThreadLocal;
	if (pThreadLocal == 0
	    || pThreadLocal->m_pcoThreadPool == 0
	    || pThreadLocal->m_pcoThreadPool->m_pThreads == 0
	    || pThreadLocal->m_pcoThreadPool->m_pStartRoutine == 0
	    || pThreadLocal->m_pcoThreadPool->m_nMaxThreads <= pThreadLocal->m_nThreadNr)
	{
		cout << "threadRoutine: invalid parameter\n";
		return NULL;
	}
	pthread_cleanup_push(CleanRoutine, p_pThreadLocal);		// set the thread clean routine
	CThreadPool* pcoThreadPool = pThreadLocal->m_pcoThreadPool;
	UInt nThreadNr = pThreadLocal->m_nThreadNr;
	ThreadInfo* pThreadInfo = &(pcoThreadPool->m_pThreads[nThreadNr]);
	for (; ; )				// endless loop; run until the thread is killed
	{
		pcoThreadPool->Debug("threadRoutine: waiting", false, 0, true, nThreadNr);
		sem_wait(&(pThreadInfo->m_Start));		// wait for signal to start
		try										// start working; call m_pStartRoutine
		{
			pcoThreadPool->Debug("threadRoutine: start, arg: ", true, pThreadInfo->m_pArg,
			                     true, nThreadNr);
			pcoThreadPool->m_pStartRoutine(pThreadInfo->m_pArg);
			pcoThreadPool->LockMutex();
			pThreadInfo->m_bWorking = false;
			pcoThreadPool->m_nWorkingThreads--;
			pcoThreadPool->Debug("threadRoutine: exit", false, 0, true, nThreadNr);
			pcoThreadPool->UnlockMutex();
		}
		catch (ExThread& coEx)
		{
			pcoThreadPool->UnlockMutex();
		}
	}
	pthread_cleanup_pop(1);
	return NULL;
}

// CleanRoutine: called when thread is signalled to terminate
// Parameters: void* p_pThreadLocal - pointer to a ThreadLocal structure; this is passed by
//		createThread method to threadRoutine
void CThreadPool::CleanRoutine(void* p_pThreadLocal)
{
	ThreadLocal* pThreadLocal = (ThreadLocal*) p_pThreadLocal;
	if (pThreadLocal == 0
	        || pThreadLocal->m_pcoThreadPool == 0
	        || pThreadLocal->m_pcoThreadPool->m_pThreads == 0)
	{
		cout << "cleanRoutine: invalid parameter";
		return ;
	}
	CThreadPool* pcoThreadPool = pThreadLocal->m_pcoThreadPool;
	UInt nThreadNr = pThreadLocal->m_nThreadNr;
	ThreadInfo* pThreadInfo = &(pcoThreadPool->m_pThreads[nThreadNr]);
	delete pThreadLocal;
	try
	{
		pcoThreadPool->LockMutex();
		pThreadInfo->m_bWorking = false;
		pThreadInfo->m_bCreated = false;
		pThreadInfo->m_pArg = 0;
		pcoThreadPool->m_nCreatedThreads--;
		pcoThreadPool->UnlockMutex();
	}
	catch (ExThread& coEx)
	{
		pcoThreadPool->UnlockMutex();
	}
}

// CreateThread: create a new thread
// Parameters: UInt p_nIndex - position in the pool
void CThreadPool::CreateThread(UInt p_nIndex) throw(ExThreadNotFree, ExThreadErrCreate)
{
	try
	{
		LockMutex();
		if (p_nIndex >= m_nMaxThreads)
			throw ExThread(ThreadSvRetry, "Thread index exceeds limits.");
		if (m_nCreatedThreads >= m_nMaxThreads)
			throw ExThreadNotFree();
		if (m_pThreads[p_nIndex].m_bCreated)
			return ;
		pthread_attr_t threadAttr;
		pthread_attr_init(&threadAttr);
		pthread_attr_setdetachstate(&threadAttr, PTHREAD_CREATE_DETACHED);
		int nRes = pthread_create(&m_pThreads[p_nIndex].m_nThread, &threadAttr,
		                          ThreadRoutine, new ThreadLocal(this, p_nIndex));
		pthread_attr_destroy(&threadAttr);
		if (nRes != 0)
			throw ExThreadErrCreate();
		m_pThreads[p_nIndex].m_bCreated = true;
		m_pThreads[p_nIndex].m_bWorking = false;
		m_nCreatedThreads++;
		UnlockMutex();
	}
	catch (ExThread& coEx)
	{
		UnlockMutex();
		throw coEx;
	}
}
