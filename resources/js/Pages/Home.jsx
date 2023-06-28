import Navigation from '@/Components/Navigation';

import { Head } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import Loading from '@/Components/Loading';
import Api from '@/lib/Api';

export default function Home() {

    const [user, setUser] = useState(null)

    useEffect(() => {
        Api.getCurrentUser()
            .then(res => {
                setUser(res.data)
            })
            .catch(e => {
                alert(e.message ?? "Something went wrong")
                setUser({})
            })
    }, [])

    return (
        <>
            <Head title="Home" />
            <Navigation />

            <div className="container mx-auto p-5">
                {user ?
                    <div>
                        <div className="content rounded bg-gray-100 shadow-lg p-4">
                            <h1 className="text-xl font-bold mb-6">Welcome to the App!!</h1>
                            <h4 className="text-xl font-bold mb-6">You're logged in as {user.email}</h4>
                        </div>
                    </div>
                    :
                    <Loading />
                }

            </div>
        </>
    );

}