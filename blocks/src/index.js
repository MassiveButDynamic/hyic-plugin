import { registerBlockType } from '@wordpress/blocks';
import { collections } from '@wordpress/api'; 
import apiFetch from '@wordpress/api-fetch';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';


async function getEventTitle() {
    const evTitle = (await apiFetch( { path: '/wp/v2/hyic_events' } ))[0].title.rendered;
    console.log(evTitle);
    return <div>{evTitle}</div>;
}
// apiFetch( { path: '/wp/v2/hyic_events' } ).then( posts => {
//     console.log( 'Events', posts );
// } );
// var testVar = posts[0].title.rendered || 'Testtitle';


registerBlockType( 'hyic/events-carousel', {
    title: 'Events-Carousel',
    icon: 'smiley',
    category: 'layout',
    edit: withSelect( ( select ) => {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'hyic_event' ),
        };
    } )( ( { posts } ) => {
        const blockProps = useBlockProps();
        const postElements = [];

        const MAX_NUMBER_OF_EVENTS = 3;
        if(posts) {
            for(const p of posts.slice(0, MAX_NUMBER_OF_EVENTS)) {
                const eventDateString = assembleEventDateString(p);
                const eventDeadlineString = (new Date(p._hyic_event_registration_deadline)).toLocaleDateString();

                postElements.push(
                    <div class='hyic-event-card'>
                        <div class='hyic-event-card-image-wrapper'>
                            <img src={p.thumbnail_url}></img>
                        </div>
                        <div class='hyic-event-card-text-wrapper'>
                            <span class='hyic-event-card-title'>{p.title.rendered}</span>
                            <span class='hyic-event-card-time'>{eventDateString}</span>
                            <span class='hyic-event-card-deadline'>Anmeldung bis: {eventDeadlineString}</span>
                        </div>
                        <a class='hyic-event-card-button' href={p.link}>
                            <span>Jetzt anmelden</span>
                        </a>
                    </div>
                );
            }
        }
        console.log(posts);
    
        return (
            <div { ...blockProps }>
                { ! posts && 'Lade Events...' }
                { posts && posts.length === 0 && 'Keine Events' }
                {postElements}
            </div>
        )
    
    } ),
    //save: () => <div>Hello world</div>,
} );


function assembleEventDateString(event) {
    const start = new Date(event._hyic_event_start_date + ' ' + event._hyic_event_start_time);
    const end = new Date(event._hyic_event_end_date + ' ' + event._hyic_event_end_time);

    if(event._hyic_event_all_day=='true') {
        if(event._hyic_event_start_date == event._hyic_event_end_date) {
            return `Am ${start.toLocaleDateString()}`
        } else {
            return `Vom ${start.toLocaleDateString()} bis ${end.toLocaleDateString()}`
        }
    } else {
        if(event._hyic_event_start_date == event._hyic_event_end_date) {
            return `Am ${start.toLocaleDateString()} von ${event._hyic_event_start_time} bis ${event._hyic_event_end_time} Uhr`
        } else {
            return `Von ${start.toLocaleDateString()} ${event._hyic_event_start_time} Uhr bis ${end.toLocaleDateString()} ${event._hyic_event_end_time} Uhr`
        }
    }
}