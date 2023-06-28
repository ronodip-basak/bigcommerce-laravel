import Loading from '@/Components/Loading';
import Navigation from '@/Components/Navigation';
import Table from '@/Components/Table';
import Api from '@/lib/Api';

import { Head } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';

export default function List() {

    const [loading, setLoading] = useState(true)

    const [products, setProducts] = useState([]);
    const [pagination, setPagination] = useState({
        per_page: 10,
        current_page: 1,
        total_pages: 1,
        total: 0
    });

    const tableHeaders = [
        {
            label: "Name",
            callback: data => data.name
        },
        {
            label: 'SKU',
            callback: data => data.sku
        },
        {
            label: 'Price',
            callback: data => data.price
        },
        {
            label: 'Description',
            callback: data => <div dangerouslySetInnerHTML={{__html: data.description}} />
        }
    ];

    const loadProducts = async (pagination) => {
        setLoading(true)
        try {
            
            const res = await Api.getResource('v3/catalog/products', {
                limit: pagination.per_page,
                page: pagination.current_page
            })
            // console.log(res);
            setProducts(res.data.data)
            setPagination({...res.data.meta.pagination, per_page: pagination.per_page})
            setLoading(false)
        } catch (e) {
            alert(e.message ?? "Something went wrong")
            setLoading(false)
        }
    }

    useEffect(() => {
        setLoading(true)
        loadProducts(pagination)

    }, [])

    const changePageTo = (page) => {
        if (page > pagination.total_pages) {
            page = pagination.total_pages;
        }

        if(page < 1){
            page = 1;
        }
        const myPagination = { ...pagination, current_page: page }

        setPagination(myPagination)
        loadProducts(myPagination)
    }
    return (
        <>
            <Head title="Order List" />
            <Navigation />
            <div className="container mx-auto p-5 w-full">
                <div className="content col-span-3 grid-col-3 rounded bg-gray-100 shadow-lg p-4 w-full overflow-x-scroll">
                    <h2 className="text-xl font-bold mb-6">List of Products</h2>
                    {
                        loading ?
                            <Loading />
                            :
                            <Table tableHeaders={tableHeaders} tableData={products} />

                    }

                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            onClick={() => {
                                changePageTo(pagination.current_page - 1)
                            }}
                            
                        >
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            onClick={() => {
                                changePageTo(pagination.current_page + 1)
                            }}
                        >
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
        </>
    );

}