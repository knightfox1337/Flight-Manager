
import { useEffect, useState } from 'react'
// importing react calendar timeline to render output
import Timeline from 'react-calendar-timeline'
// importing calendar provided styles
import 'react-calendar-timeline/lib/Timeline.css'
// importing overall styles
import './App.css'
// temp importing moment, to render test dates
import moment from 'moment'

function App() {

  const [groups, setGroups] = useState(null)
  const [items, setitems] = useState(null)
  const [loading, setLoading] = useState(true)
  const [calendarStartTime, setCalendarStartTime] = useState(null)
  const apiEndpoint = 'http://localhost:3001/fake-api-endpoint.php'


  useEffect(() => {
    // querying endpoint for data
    fetch(apiEndpoint)
      .then(resp => resp.json())
      .then(respjson => {
        // formatting data as json then setting:
        // Calendar start time
        // Group data
        // item data
        // disabling loading
        setCalendarStartTime(respjson.calendarStart)
        setGroups(respjson.groups)
        setitems(respjson.items)
        setLoading(false)
      })
  }, [])


  // using itemRenderer overwrite to style item blocks & add additional information
  const itemRenderer = ({ item, timelineContext, itemContext, getItemProps, getResizeProps }) => {
    const { left: leftResizeProps, right: rightResizeProps } = getResizeProps()
    return (
      <div {...getItemProps(item.itemProps)}>
        {itemContext.useResizeHandle ? <div {...leftResizeProps} /> : ''}

        <div
          className="rct-item-content"
          style={{ maxHeight: `${itemContext.dimensions.height}` }}
        >
          <div className='contain'>
              <div className="topLeft">{item.itemProps.origin}</div>
              <div className="topRight">{item.itemProps.destination}</div>
              <div className="middle">{item.itemProps.flight_time}</div>
              <div className="bottomLeft">{item.itemProps.departure_time}</div>
              <div className="bottomRight">{item.itemProps.arrival_time}</div>
          </div>
        </div>

        {itemContext.useResizeHandle ? <div {...rightResizeProps} /> : ''}
      </div>
    )
  }

  return (
    <div className="App">
    {loading
    ? 
      <span className="loader"></span>
    :
      <Timeline
        groups={groups}
        items={items}
        defaultTimeStart={moment.unix(calendarStartTime)}
        defaultTimeEnd={moment.unix(calendarStartTime).add(1,'days')}
        lineHeight={120}
        canMove={false}
        itemRenderer={itemRenderer}
      />
    }
    </div>
  )
}

export default App
